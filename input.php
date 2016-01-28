<form action="?">
	<div style="float: left; margin-left:5px;">
		Source language:<br/>
		<select class="form-control" name="srclang" />
			<option>English</option>
			<option>German</option>
			<option>French</option>
		</select>
	</div>
	<div style="float: left; margin-left:5px;">
		Target language:<br/>
		<select class="form-control" name="trglang" />
			<option>Latvian</option>
		</select>
	</div>
	<br style="clear: both;"/><br/>
	<div class="mt">
		Source sentence:<br/>
		<textarea style="width:604px;" class="form-control" name="src" placeholder="Required"></textarea><br/>
	</div>

	<br style="clear: both;"/>
	<input type="hidden" name="id" value="inputprocess"/>
	<input style="margin-left:5px;" type="submit" class="btn btn-sm btn-default" value="Next!"/>
</form>
<br/><br/>