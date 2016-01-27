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
	<div style="float: left; margin-left:5px;">
		Use:<br/>
		<div class="checkbox">
			<label><input type="checkbox" name="google" >Google Translate</label>
			<label><input type="checkbox" name="bing" >Bing Translator</label>
			<label><input type="checkbox" name="yandex" >Yandex Translate</label>
			<label><input type="checkbox" name="hugo" >Hugo</label>
		</div>
	</div>
	<br style="clear: both;"/>
	<div class="mt">
		Source sentence:<br/>
		<textarea style="width:604px;" class="form-control" name="sentence"></textarea><br/>
	</div>

	<br style="clear: both;"/>
	<input type="hidden" name="id" value="apiresult"/>
	<input style="margin-left:5px;" type="submit" class="btn btn-sm btn-default" value="Translate!"/>
</form>
