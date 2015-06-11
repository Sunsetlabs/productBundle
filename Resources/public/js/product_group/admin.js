var productAdmin = {
    init: function(){
        // new images
        productAdmin.imageCollection = $('div.images');
        productAdmin.imageCollection.each(function(){
            $(this).data('index', $(this).find(':input').length);
        })

        // new attributes
        productAdmin.attributeCollection = $('div.attributes');
        productAdmin.attributeAddLink = $('<a href="#" class="add_attribute_link btn btn-xs">Agregar Attributo</a>');
        productAdmin.attributeNewLinkTr = $('<div class="btn-group col-lg-12"></div>').append(productAdmin.attributeAddLink);
        productAdmin.attributeCollection.append(productAdmin.attributeNewLinkTr);
        productAdmin.attributeCollection.data('index', productAdmin.attributeCollection.find(':input').length);

        if (productAdmin.attributeCollection.data('add') == 1) {
            productAdmin.addAttribute();
        }

        productAdmin.observer();
    },
    observer: function(){
        productAdmin.observeAddImage();
        productAdmin.attributeAddLink.on('click', function(e){
            e.preventDefault();
            productAdmin.addAttribute();
        })

        $('.image-picker').find('input[type="file"]').change(function(e){
            e.preventDefault();
            productAdmin.readURL(this, '.image-picker');
        })

        $('.remove_image').unbind('click').on('click', function(e){ 
            e.preventDefault();
            $(this).parents('div').first().remove();  
        })
        $('.image_toogle').unbind('click').on('click', function(e){
            e.preventDefault();
            $imgs = $(this).parents('.attribute').first().find('.asoc-image');
            $imgs.slideToggle('fast');
        })

        $('.remove_attribute').unbind('click').on('click', function(e){
            e.preventDefault();
            $(this).parents('.attribute').first().remove();  
        })

        // $('.image-picker-multple').imagepicker();

        productAdmin.observerCallback();
    },
    observeAddImage: function(){
        $('#add-image').unbind('change').change(function(e){
            e.preventDefault();
            $this = $(this).clone();
            $(this).parent().append($this);
            productAdmin.addImage($(this));
            productAdmin.observeAddImage();
        })
    },
    addAttribute: function(){
        var prototype = productAdmin.attributeCollection.data('prototype');
        var index = productAdmin.attributeCollection.data('index');
        var newForm = prototype.replace(/__name__/g, index);
        productAdmin.attributeCollection.data('index', index + 1);
        $newForm = $(newForm);
        productAdmin.attributeNewLinkTr.before($newForm);

        // $('.image-picker-multple').imagepicker();

        $('.remove_attribute').unbind('click').on('click', function(e){
            e.preventDefault();
            $(this).parents('.attribute').first().remove();  
        })
        $('.image_toogle').unbind('click').on('click', function(e){
            e.preventDefault();
            $imgs = $(this).parents('.attribute').first().find('.asoc-image');
            $imgs.slideToggle('fast');
        })

        productAdmin.addAttributeCallback();
    },
    addImage: function($input){
        var proto = productAdmin.imageCollection.data('prototype');
        var index = productAdmin.imageCollection.data('index');
        var newForm = proto.replace(/__name__/g, index);
        productAdmin.imageCollection.data('index', index + 1);
        $newImage = $(newForm);

        $file = $newImage.find('input[type="file"]');
        $input.attr('id',$file.attr('id'));
        $input.attr('name',$file.attr('name'));
        $file.replaceWith($input);

        productAdmin.imageCollection.prepend($newImage);
        $newImage.find('input[type="file"]').unbind('change').change(function(e){
            e.preventDefault();
            productAdmin.readURL(this, '.image-picker');
        });

        $input.change();
        

        $('.remove_image').unbind('click').on('click', function(e){
            e.preventDefault();
            $(this).parents('div').first().remove();
        })
    },
    readURL: function(input, prev_selector){
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $(input).parents(prev_selector).find('img').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    },
    addAttributeCallback: function(){},
    observerCallback: function(){},
}

productAdmin.init();
