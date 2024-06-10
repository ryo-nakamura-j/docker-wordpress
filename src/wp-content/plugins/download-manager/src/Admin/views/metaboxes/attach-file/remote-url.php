<?php
if(!defined("ABSPATH")) die("Shit happens!");
?>
<div class="w3eden">
    <div class="input-group" style="margin-bottom: 10px"><input type="url" id="rurl" class="form-control" placeholder="Insert URL" style="margin-right: -1px"><span class="input-group-btn"><button type="button" id="rmta" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></span></div>
</div>

<script>

    jQuery(function($){
        //jQuery( "#ftabs" ).tabs();

        $('#rmta').click(function(){
            var d = new Date();
            var ID = d.getTime();
            var file = $('#rurl').val().replace(/[\<\>]/g, '');
            var filename = file;
            $('#rurl').val('');

            let regex = new RegExp("^(ftp:\/\/|http:\/\/|https:\/\/)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$");
            if(!regex.test(file)){
                alert("Invalid url");
                return false;
            }

            var ext = file.split('.');
            ext = ext[ext.length-1];
            if(ext.indexOf('://')) ext = 'url';
            else
            if(ext.length==1 || ext==filename || ext.length>4 || ext=='') ext = '_blank';

            var icon = "<?php echo WPDM_BASE_URL; ?>file-type-icons/"+ext.toLowerCase()+".png";
            var title = file;
            title = title.split('/');
            title = title[title.length - 1];

            var _file = {};
            _file.filetitle = (title);
            _file.filepath = encodeURI(file);
            _file.fileindex = ID;
            _file.preview = icon;
            wpdm_attach_file(_file);



        });

    });

</script>