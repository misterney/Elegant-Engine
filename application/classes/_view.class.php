<?
class View{
	
	public $vars;
	public $templates_root_dir; // templates_root_dir указывать без закрывающего слэша!
	public $templates_current_dir;
	public $TIMES;
	private $profiling;
	private $predecessor; // объект шаблонизатора верхнего уровня, из которого делался вызов текущего
	
	function __construct(
			$vars = array(), 
			$templates_root = FALSE, 
			$profiling = FALSE,
			$predecessor = FALSE
		) {
	
		$this->vars = $vars;
		
		if ($templates_root) // корневой каталог шаблонов
			$this->templates_root_dir = $this->template_real_path($templates_root);
		else { // если не указан, то принимается каталог файла, в котором вызван websun
			// С 0.50 - НЕ getcwd()! Т.к. текущий каталог - тот, откуда он запускается,
			// $this->templates_root_dir = getcwd();
			foreach (debug_backtrace() as $trace) {
				if (preg_match('/^websun_parse_template/', $trace['function'])) {
					$this->templates_root_dir = dirname($trace['file']);
					break;
				}
			}
			
			if (!$this->templates_root_dir) {
				foreach (debug_backtrace() as $trace) {
					if ($trace['class'] == 'View') {
						$this->templates_root_dir = dirname($trace['file']);
						break;
					}
				}
			}
		}
		
		$this->templates_current_dir = $this->templates_root_dir . '/';
		$this->profiling = $profiling;
		
		$this->predecessor = $predecessor;
	}
	
	
	function parse_template($template) {
		if ($this->profiling)
			$start = microtime(1);
		
		$template = preg_replace('/ \\/\* (.*?) \*\\/ /sx', '', $template); /**ПЕРЕПИСАТЬ ПО JEFFREY FRIEDL'У !!!**/
		
		$template = str_replace('\\\\', "\x01", $template); 	// убираем двойные слэши
		$template = str_replace('\*', "\x02", $template);	// и экранированные звездочки
		
		$template = preg_replace_callback( // дописывающие модули
			'/
			{\*
			&(\w+)
			(?P<args>\([^*]*\))?
			\*}
			/x', 
			array($this, 'addvars'), 
			$template
			);
		
		$template = $this->find_and_parse_cycle($template);
		
		$template = $this->find_and_parse_if($template);
		
		$template = preg_replace_callback( // переменные, шаблоны и модули
				'/
				{\*
				(.*?)
				\*}
				/x', 
				/* подумать о том, чтобы вместо (.*?) 
				   использовать жадное, но более строгое
				   (
					(?:
						[^*]*+
						|
						\*(?!})
					 )+
				)
				*/
				array($this, 'parse_vars_templates_modules'), 
				$template
			);
		
		$template = str_replace("\x01", '\\\\', $template); // возвращаем двойные слэши обратно
		$template = str_replace("\x02", '*', $template); // а звездочки - уже без экранирования 
		
		if ($this->profiling AND !$this->predecessor) {
			$this->TIMES['_TOTAL'] = round(microtime(1) - $start, 4) . " s";
			// ksort($this->TIMES);
			echo '<pre>' . print_r($this->TIMES, 1) . '</pre>';
		}
		
		return $template;
	}
	
	// дописывание массива переменных из шаблона
	// (хак для Бурцева)
	function addvars($matches) {
		if ($this->profiling) 
			$start = microtime(1);
		
		$module_name = 'module_'.$matches[1];
		# ДОБАВИТЬ КЛАССЫ ПОТОМ
		$args = (isset($matches['args'])) 
			? explode(',', mb_substr($matches['args'], 1, -1) ) // убираем скобки
			: array();
		$this->vars = array_merge(
			$this->vars, 
			call_user_func_array($module_name, $args)
			); // call_user_func_array быстрее, чем call_user_func
		
		if ($this->profiling) 
			$this->write_time(__FUNCTION__, $start, microtime(1));
		
		return TRUE;
	}
	
	
	function var_value($string) {
		
		if ($this->profiling)
			$start = microtime(1);
		
		if (mb_substr($string, 0, 1) == '=') { # константа
			$C = mb_substr($string, 1);
			$out = (defined($C)) ? constant($C) : '';
		}
		
		// можно делать if'ы:
		// {*var_1|var_2|"строка"|134*}
		// сработает первая часть, которая TRUE
		elseif (mb_strpos($string, '|') !== FALSE) {
			$f = __FUNCTION__;
			
			foreach (explode('|', $string) as $str) {
				// останавливаемся при первом же TRUE
				if ($val = $this->$f($str)) 
					break; 
			}
			
			$out = $val;
		}
		
		elseif ( # скалярная величина
			mb_substr($string, 0, 1) == '"'
			AND 
			mb_substr($string, -1) == '"'
		)
			$out = mb_substr($string, 1, -1);
		
		elseif (mb_strlen($string) AND !preg_match('/\D/', $string))
			# опять же скалярная величина (число)
				// (0.1.17 - numeric() вместо этого нельзя,
				// т.к. тогда могу быть неполадки с разворотом циклов 
				// - точки с числами могут встать хитрым образом
				// и перепутаться с этим),
				// а is_int() нельзя, т.к. возвращает 
				// FALSE для строк типа '1'
			$out = $string;
			
		else {
			if (mb_substr($string, 0, 1) == '$') { 
				// глобальная переменная
				$string = mb_substr($string, 1);
				$value = $GLOBALS;
			}
			else 
				$value = $this->vars;
				
			// допустимы выражения типа {*var^COUNT*}
			// (вернет count($var)) )
			if (mb_substr($string, -6) == '^COUNT') {
				$string = mb_substr($string, 0, -6);
				$return_mode = 'count';
			}
			else
				$return_mode = FALSE; // default
			
			$rawkeys = explode('.', $string);
			$keys = array();
			foreach ($rawkeys as $v) { 
				if ($v !== '') 
					$keys[] = $v; 
			}
			// array_filter() использовать не получается, 
			// т.к. числовой индекс 0 она тоже считает за FALSE и убирает
			// поэтому нужно сравнение с учетом типа
			
			// пустая строка указывает на корневой массив
			foreach ($keys as $k) {
				if (is_array($value) AND isset($value[$k]) ) $value = $value[$k]; 
				else { $value = NULL; break; }
			}
			
			// в зависимости от $return_mode действуем по-разному:
			$out = (!$return_mode)
				// возвращаем значение переменной (обычный случай)
				? $value
				
				// возвращаем число элементов в массиве
				: ( is_array($value) ? count($value) : FALSE )
				
				;
		}
		
		if ($this->profiling) 
			$this->write_time(__FUNCTION__, $start, microtime(1));
		
		return $out;
	}
	
	function find_and_parse_cycle($template) {
		if ($this->profiling) 
			$start = microtime(1);
		// пришлось делать специальную функцию, чтобы реализовать рекурсию
		$out = preg_replace_callback(
			'/
			{%\* ([^*]*) \*}
			(.*?)
			{\* \1 \*%}
			/sx', 
			array($this, 'parse_cycle'), 
			$template
			);
		
		if ($this->profiling) 
			$this->write_time(__FUNCTION__, $start, microtime(1));
		
		return $out;
	}
	
	function parse_cycle($matches) {
		
		if ($this->profiling) 
			$start = microtime(1);
		
		$array_name = $matches[1];
		$array = $this->var_value($array_name);
		$array_name_quoted = preg_quote($array_name);
		
		if ( ! is_array($array) ) 
			return FALSE;
		
		$parsed = '';
		
		$dot = ( $array_name != '' AND $array_name != '$' ) 
		     ? '.' 
		     : '';
		
		foreach ($array as $key => $value) {
			$parsed .= preg_replace(
					array(// массив поиска
							"/(?<=[*=<>|&%])\s*$array_name_quoted\:\^KEY(?!\w)/",     
							"/(?<=[*=<>|&%])\s*$array_name_quoted\:/"
						), 
					array(// массив замены
							'"' . $key . '"',               // preg_quote для ключей нельзя, 
							$array_name . $dot . $key . '.' // т.к. в ключах бывает удобно
						),                                 // хранить некоторые данные,
					$matches[2]                           // а preg_quote слишком многое экранирует
			   );
		}
		$parsed = $this->find_and_parse_cycle($parsed);
		
		if ($this->profiling) 
			$this->write_time(__FUNCTION__, $start, microtime(1));
		
		return $parsed;
	}
	
	function find_and_parse_if($template) {
		
		if ($this->profiling)
			$start = microtime(1);
		
		$out = preg_replace_callback( 
				'/
				{ (\?\!?) \*([^*]*)\* } # открывающее условие
				(.*?)                   # тело if
				{\*\2\* \1}             # закрывающее условие
				/sx',
				array($this, 'parse_if'), 
				$template
			);
		
		if ($this->profiling) 
			$this->write_time(__FUNCTION__, $start, microtime(1));
		
		return $out;
	}
	
	
	function parse_if($matches) {
		// 1 - ? или ?!
		// 2 - тело условия
		// 3 - тело if
		
		if ($this->profiling) 
			$start = microtime(1);
		
		$final_check = FALSE;
		
		$separator = (strpos($matches[2], '&'))
		           ? '&'  // "AND"
		           : '|'; // "OR"
		$parts = explode($separator, $matches[2]);
		$parts = array_map('trim', $parts); // убираем пробелы по краям
		
		$checks = array();
		
		foreach ($parts as $p) 
			$checks[] = $this->check_if_condition_part($p);
		
		if ($separator == '|') // режим "OR" 
			$final_check = in_array(TRUE, $checks);
		
		else // режим "AND"
			$final_check = !in_array(FALSE, $checks);
		
		$result = ($matches[1] == '?') 
			     ? $final_check 
			     : !$final_check ; 
			
		$parsed_if = ($result) 
			        ? $this->find_and_parse_if($matches[3]) 
			        : '' ;
		
		if ($this->profiling) 
			$this->write_time(__FUNCTION__, $start, microtime(1));
		
		return $parsed_if;
	}
	
	function check_if_condition_part($str) {
		
		if ($this->profiling)
			$start = microtime(1);
		
		preg_match(
				'/^
				   (  
				   	"[^"*]+"       # строковый литерал
				   	
				   	|              # или
				   	
				   	[^*<>=]*+      # имя переменной
				   )  
				   
					(?: # если есть сравнение с чем-то:
						
						([=<>])  # знак сравнения 
						
						\s*
						
						(.*)     # то, с чем сравнивают
					)?
					
					$
				/x',
				$str,
				$matches
			);
		
		$left = $this->var_value(trim($matches[1]));
		
		if (!isset($matches[2]))
			$check = ($left == TRUE);
		
		else {
			
			$right = (isset($matches[3]))
					 ? $this->var_value($matches[3])
					 : FALSE ;
			
			switch($matches[2]) {
				case '=': $check = ($left == $right); break;
				case '>': $check = ($left > $right); break;
				case '<': $check = ($left < $right); break;
				default: $check = ($left == TRUE);
			}
		}
		
		if ($this->profiling) 
			$this->write_time(__FUNCTION__, $start, microtime(1));
		
		return $check;
	}
	
	function parse_vars_templates_modules($matches) {
		if ($this->profiling) 
			$start = microtime(1);
		
		// тут обрабатываем сразу всё - и переменные, и шаблоны, и модули
		$work = $matches[1]; 
		$work = trim($work); // убираем пробелы по краям
		
		if (mb_substr($work, 0, 1) == '@') { // модуль {* @name(arg1,arg2) | template *}
			$p = '/
				^
				([^(|]++) # 1 - имя модуля
				(?: \( ([^)]*+) \) \s* )? # 2 - аргументы
				(?: \| \s* (.*+) )? # 3 - это уже до конца 
				$                          # (именно .*+, а не .++)
				/x';
			if (preg_match( $p, mb_substr($work, 1), $m) ) {
				$tmp = $this->get_var_or_string($m[1]);
				$module_name = (mb_strpos($tmp, 'module_') === 0)
					? $tmp
					: "module_$tmp" ;
				$args = ( isset($m[2]) ) 
					? array_map( array($this, 'get_var_or_string'), explode(',', $m[2]) )
					: array();
				$subvars = call_user_func_array($module_name, $args);
				
				if ( isset($m[3]) )  // передали указание на шаблон
					$html = $this->call_template($m[3], $subvars);
				
				else 
					$html = $subvars; // шаблон не указан => модуль возвращает строку
			}
			else 
				$html = ''; // вызов модуля сделан некорректно
		}
		elseif (mb_substr($work, 0, 1) == '+') { 
			// шаблон - {* +*vars_var*|*tpl_var* *}
			// переменная как шаблон - {* +*var* | >*template_inside* *}
			$html = '';
			$parts = preg_split(
					'/(?<=[\*\s])\|(?=[\*\s])/', // вертикальная черта
					mb_substr($work, 1) // должна ловиться только как разделитель
					// между переменной и шаблоном, но не должна ловиться 
					// как разделитель внутри нотации переменой или шаблона
					// (например, {* + *var1|$GLOBAL* | *tpl1|tpl2* *}
				); 
			$parts = array_map('trim', $parts); // убираем пробелы по краям
			if ( !isset($parts[1]) ) { 	// если нет разделителя (|) - значит, 
							                  // передали только имя шаблона +template
							                  
				$html = $this->call_template($parts[0], $this->vars);
			}
			else {
				$varname_string = mb_substr($parts[0], 1, -1); // убираем звездочки
				// {* +*vars* | шаблон *} - простая передача переменной шаблону
				// {* +*?vars* | шаблон *} - подключение шаблона только в случае, если vars == TRUE
				// {* +*%vars* | шаблон *} - подключение шаблона не для самого vars, а для каждого его дочернего элемента 
				$indicator = mb_substr($varname_string, 0, 1);
				if ($indicator == '?') { 
					if ( $subvars = $this->var_value( mb_substr($varname_string, 1) ) )
						// 0.1.27 $html = $this->parse_child_template($tplname, $subvars);
						$html = $this->call_template($parts[1], $subvars);
				}
				elseif ($indicator == '%') {
					if ( $subvars = $this->var_value( mb_substr($varname_string, 1) ) ) {
						foreach ( $subvars as $row ) { 
							// 0.1.27 $html .= $this->parse_child_template($tplname, $row);
							$html .= $this->call_template($parts[1], $row);
						}
					}
				}
				else {
					$subvars = $this->var_value( $varname_string );
					// 0.1.27 $html = $this->parse_child_template($tplname, $subvars);
					$html = $this->call_template($parts[1], $subvars);
				}
			}
		}
		else 
			$html = $this->var_value($work); // переменная (+ константы - тут же)
			
		if ($this->profiling) 
			$this->write_time(__FUNCTION__, $start, microtime(1));

		return $html;
	}
	
	function call_template($template_notation, $vars) {
		if ($this->profiling) 
			$start = microtime(1);

		// $template_notation - либо путь к шаблону,
		// либо переменная, содержащая путь к шаблону,
		// либо шаблон прямо в переменной - если >*var*
		$c = __CLASS__; // нужен объект этого же класса - делаем
		$subobject = new $c(
				$vars, 
				$this->templates_root_dir,
				$this->profiling,
				$this
			);
		
		$template_notation = trim($template_notation);
		
		if (mb_substr($template_notation, 0, 1) == '>') { 
			// шаблон прямо в переменной
			$v = mb_substr($template_notation, 1);
			$subtemplate = $this->get_var_or_string($v);
			$subobject->templates_current_dir = $this->templates_current_dir;
		}
		else {
			$path = $this->get_var_or_string($template_notation);
			$subobject->templates_current_dir = pathinfo($this->template_real_path($path), PATHINFO_DIRNAME ) . '/';
			$subtemplate = $this->get_template($path);
		}
		
		$result = $subobject->parse_template($subtemplate);
		
		if ($this->profiling) 
			$this->write_time(__FUNCTION__, $start, microtime(1));
		
		return $result;
	}
	
	function get_var_or_string($str) {
		// используется, в основном, 
		// для получения имён шаблонов и модулей
		if ($this->profiling) 
			$start = microtime(1);
		
		if ( mb_substr($str, 0, 1) == '*' AND mb_substr($str, -1) == '*')
			$out = $this->var_value( mb_substr($str, 1, -1) ); // если вокруг есть звездочки - значит, перменная
			
		else // нет звездочек - значит, обычная строка-литерал
			$out = ( mb_substr($str, 0, 1) == '"'  AND mb_substr($str, -1) == '"') // строка
			     ? mb_substr($str, 1, -1)
			     : $str ;
		
		if ($this->profiling) 
			$this->write_time(__FUNCTION__, $start, microtime(1));
		
		return $out;
	}
	
	function get_template($tpl) {
		if ($this->profiling) 
			$start = microtime(1);
		
		if (!$tpl) return FALSE;
		$tpl_real_path = $this->template_real_path($tpl);
		// return rtrim(file_get_contents($tpl_real_path), "\r\n");

		// (убираем перенос строки, присутствующий в конце любого файла)
		$out = preg_replace(
				'/\r?\n$/',
				'',
				file_get_contents($tpl_real_path)
			);
		
		if ($this->profiling) 
			$this->write_time(__FUNCTION__, $start, microtime(1));
		
		return $out;
	}
	
	
	function template_real_path($tpl) {
		// функция определяет реальный путь к шаблону в файловой системе
		// первый символ пути к шаблону определяет тип пути 
		// если в начале адреса есть / - интерпретируем как абсолютный путь ФС
		// если второй символ пути - двоеточие (путь вида C:/ - Windows) - 
		// также интепретируем как абсолютный путь ФС
		// если есть ^ - отталкиваемся от $templates_root_dir
		// если $ - от $_SERVER[DOCUMENT_ROOT]
		// во всех остальных случаях отталкиваемся от каталога текущего шаблона - templates_current_dir
		if ($this->profiling) 
			$start = microtime(1);
		
		$dir_indicator = mb_substr($tpl, 0, 1);
		
		$adjust_tpl_path = TRUE;
		
		if ($dir_indicator == '^') $dir = $this->templates_root_dir;
		elseif ($dir_indicator == '$') $dir = $_SERVER['DOCUMENT_ROOT'];
		elseif ($dir_indicator == '/') { $dir = ''; $adjust_tpl_path = FALSE; } // абсолютный путь для ФС 
		else {
			if (mb_substr($tpl, 1, 1) == ':') // Windows - указан абсолютный путь - вида С:/...
				$dir = '';
			else  
				$dir = $this->templates_current_dir;  
			
			$adjust_tpl_path = FALSE; // в обоих случаях строку к пути менять не надо
		}
		
		if ($adjust_tpl_path) $tpl = mb_substr($tpl, 1);
		
		$tpl_real_path = $dir . $tpl;
		
		if ($this->profiling) 
			$this->write_time(__FUNCTION__, $start, microtime(1));
		
		return $tpl_real_path;
	}
	
	function write_time($method, $start, $end) {
		
		//echo ($this->predecessor) . '<br>';
		
		
		if (!$this->predecessor)
			$time = &$this->TIMES;
		
		else
			$time = &$this->predecessor->TIMES ;
		
		if (!isset($time[$method]))
			$time[$method] = array(
					'n' => 0,
					'last' => 0,
					'total' => 0,
					'avg' => 0
				);
			
		$time[$method]['n'] += 1;
		$time[$method]['last'] = round($end - $start, 4);
		$time[$method]['total'] += $time[$method]['last'];
		$time[$method]['avg'] = round($time[$method]['total'] / $time[$method]['n'], 4) ;
	}


	function websun_parse_template(
			$data, 
			$template_code, 
			$templates_root_dir = FALSE,
			$profiling = FALSE
		) {
		// функция-обёртка для быстрого вызова класса
		// принимает шаблон непосредственно в виде кода
		$W = new websun($data, $templates_root_dir, $profiling);
		$string = $W->parse_template($template_code);
		return $string;
	}



	function websun_parse_template_path(
			$data, 
			$template_path, 
			$templates_root_dir = FALSE,
			$profiling = FALSE
		) {

		// функция-обёртка для быстрого вызова класса
		// принимает шаблон в виде пути к нему
	
		$W = new websun($data, $templates_root_dir, $profiling);
		$tpl = $W->get_template($template_path);
		$W->templates_current_dir = pathinfo( $W->template_real_path($template_path), PATHINFO_DIRNAME ) . '/';
		$string = $W->parse_template($tpl);
		return $string;
	}

	public function render(
			$path, 
			$data,
			$templates_root_dir = FALSE,
			$profiling = FALSE
		) {

		$template_path = ENGINE_PATH_MVC.'views/'.$path.'.tpl';		
		$this->data = $data;
		$this->template_path = $template_path;
		
		$this->__construct($data, $templates_root_dir, $profiling);

		$tpl = $this->get_template($template_path);
		$this->templates_current_dir = pathinfo($this->template_real_path($template_path), PATHINFO_DIRNAME ) . '/';
		$string = $this->parse_template($tpl);
		
		echo $string;

		/* $page = explode("/", $path);
		if(file_exists(ENGINE_PATH_MVC.'views/'.$template)){
			include_once(ENGINE_PATH_MVC.'views/'.$template);
		}
		*/
	}
}