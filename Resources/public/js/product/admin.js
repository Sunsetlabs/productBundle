var padmin = {
    init: function(){
        padmin.imageCollection = $('.images');
        padmin.imageCollection.data('index', padmin.imageCollection.find(':input').length)
        padmin.observer()
    },
    observer: function(){
        padmin.observeAddImage();
        $('.image-picker').each(function(){
            padmin.observeImageChange($(this).find('input[type="file"]'));
            padmin.observeRemoveImage($(this));
        });
    },
    observeImageChange: function($input){
        $input.change(function(e){
            e.preventDefault()
            padmin.readURL(this, '.image-picker')
        })
    },
    observeAddImage: function(){
        $('#add-image').unbind('change').change(function(e){
            e.preventDefault();
            $this = $(this).clone();
            $(this).parent().append($this);
            padmin.addImage($(this));
            padmin.observeAddImage();
        })
    },
    observeRemoveImage: function($image_proto_form){
        $image_proto_form.find('.remove_image').click(function(e){
            $(this).parent().remove('.image-picker');
        })
    },
    addImage: function($input){
        var proto = padmin.imageCollection.data('prototype');
        var index = padmin.imageCollection.data('index');
        var newForm = proto.replace(/__name__/g, index);

        $newImage = $(newForm);
        $file = $newImage.find('input[type="file"]');
        $input.attr('id',$file.attr('id'));
        $input.attr('name',$file.attr('name'));
        $input.unbind('change');
        
        $file.replaceWith($input);

        padmin.imageCollection.prepend($newImage);
        padmin.imageCollection.data('index', index + 1);
        padmin.observeImageChange($newImage.find('input[type="file"]'));
        padmin.observeRemoveImage($newImage);
        
        $input.change();
    },
    readURL: function(input, prev_selector){
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $(input).parents(prev_selector).find('img').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
}

padmin.init();