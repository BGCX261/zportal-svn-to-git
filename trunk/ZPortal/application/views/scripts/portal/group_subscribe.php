














<body>
<div id="doc3" class="yui-t2">
	<div id="hd" style="border: 1px solid black">
	<?php
	if (! $this->parents) {
	    ?> > <?php
	} else {
    	foreach ($this->parents as $id => $name) {
    	    ?> > <?php
    	    $url = $this->url(array('feedId' => $id));
    	    echo sprintf('<a href="%s" title="%s">%2$s</a>', $url, $this->escape($name));
    	}
    }
	?>
	</div>
	<div id="bd">
		<div class="yui-b" style="border: 1px solid black; height: 400px">
		<ul>
        <?php
        	foreach ($this->groups as $group) {
        	    ?><li><?php
        	    $url = $this->url(array('feedId' => $group['id']));
        	    echo sprintf('<a href="%s" title="%s">%2$s</a>', $url, $this->escape($group['name']));
        	    ?></li><?php
        	}
        ?>
        </ul>		
		</div>
		<div id="yui-main">
			<div class="yui-b" style="border: 1px solid black; height: 400px">
			<ul>
            <?php
            	foreach ($this->feeds as $feed) {
            	    ?><li><?php echo $this->escape($feed['name']); ?> - 
            	    <span id="feed_<?php echo $feed['id'] ?>">
            	    <?php
            	    if ($this->subscription[$feed['id']]): ?>
            	    	<a class="subscribe_link" href="<?php echo $this->url(array('feedId' => $feed['id'], 'subscribe' => true)); ?>">Add it now</a>
            	    <?php else: ?>
            	    	<span class="subscribe_link">Allready Subscribed</span>
            	    <?php endif ?>
            	    </span>
            	    <p><?php echo $this->escape($feed['description']) ?></p>
            	    <p>
            	    <a href="<?php echo $this->escape($feed['url']) ?>"><?php echo $this->escape($feed['url']) ?></a>
            	    </p>
            	    </li><?php
            	}
            ?>
            </ul>
			</div>
		</div>
	</div>
</div>
