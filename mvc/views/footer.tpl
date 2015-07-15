</div>
<script>
	$("#block_head").backstretch("http://el.uprojects.net/assets/images/bg.png");
	$("#block_1").backstretch("http://rewalls.com/pic/201207/2560x1440/reWalls.com-68325.jpg");
	$("#block_2").backstretch("http://rewalls.com/pic/201207/2560x1440/reWalls.com-68325.jpg");

	var styleBlock_head = block_head.getAttribute('style');
	var styleBlock_head = styleBlock_head.replace('z-index: 0;', 'z-index: 4;');
	block_head.setAttribute('style', styleBlock_head);
</script>
</body>