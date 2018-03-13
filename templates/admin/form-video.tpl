<form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
    {preventCsrf}

    {capture name="title" append="field_after"}
    <div class="row">
        <label class="col col-lg-2 control-label" for="input-category">{lang key='category'} <span class="text-danger">*</span></label>
        <div class="col col-lg-4">
            <select name="category_id" id="input-category">
                <option value="0">{lang key='_select_'}</option>
                {html_options options=$categories selected=$item.category_id}
            </select>
        </div>
    </div>
    {/capture}

    {include 'field-type-content-fieldset.tpl' isSystem=true datetime=true}
</form>