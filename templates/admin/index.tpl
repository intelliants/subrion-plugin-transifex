<form action="{$smarty.const.IA_SELF}" method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
	{preventCsrf}

	<div class="wrap-list">
		<div class="wrap-group">
			<div class="row">
				<label class="col col-lg-2 control-label" for="input-file">{lang key='upload_file'}</label>
				<div class="col col-lg-4">
					{ia_html_file name='file' id='input-file'}
				</div>
			</div>
			<div class="row">
				<label class="col col-lg-2 control-label" for="input-file">{lang key='language_code'}</label>
				<div class="col col-lg-4">
					<input type="text" name="lang_code" placeholder="" value="" id="input-file_url">
				</div>
			</div>
		</div>
		<div class="form-actions inline">
			<button name="upload" class="btn btn-primary"><i class="i-upload"></i> {lang key="upload_file"}</button>
		</div>
	</div>
</form>