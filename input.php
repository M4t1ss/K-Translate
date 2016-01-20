
Provide up to four machine translations:
<form action="?">
	<div class="mt">
		Source sentence:<br/>
		<textarea class="form-control" name="src" placeholder="Required"></textarea><br/>
	</div>
	<br style="clear: both;"/>
	
	<div class="mt">
		MT 1:<br/>
		<textarea class="form-control" name="mt1" placeholder="Required"></textarea><br/>
	</div>
	
	<div class="mt">
		MT 2:<br/>
		<textarea class="form-control" name="mt2" placeholder="Required"></textarea><br/>
	</div>
	<br style="clear: both;"/>
	
	<div class="mt">
		MT 3:<br/>
		<textarea class="form-control" name="mt3" placeholder="Optional"></textarea><br/>
	</div>
	
	<div class="mt">
		MT 4:<br/>
		<textarea class="form-control" name="mt4" placeholder="Optional"></textarea><br/>
	</div>
	
	<br style="clear: both;"/>
	<input type="hidden" name="id" value="inputresult"/>
	<input style="margin-left:5px;" type="submit" class="btn btn-sm btn-default" value="Combine!"/>
</form>