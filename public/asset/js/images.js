var $imageCollection;
var $addImageButton = $('<button type= "button" class="mt-3 btn btn-danger btn-rounded btn-sm my-0 add_item_link">+ Ajouter une image </button>');
var $newImage = $('<div></div>').append($addImageButton);
var indexImage= 0;

$imageCollection = $('#service_images');
$imageCollection.append($newImage);

$imageCollection.data('index', $imageCollection.find('.image').length);
$addImageButton.on('click',function(e){
    addImageFrom($imageCollection, $newImage);
});

function addImageFormDelete(arg){
    var $removeFormButton = $('<button class="mt-3 btn btn-danger btn-rounded btn-sm my-0" type="button">- Supprimer l\'image</button>');
    arg.append($removeFormButton);
    $removeFormButton.on('click', function(e){
        arg.remove();
    });
}

function addImageFrom($collectionHolder, $newItemLi) {
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var newForm = prototype;
    newForm = newForm.replace(/__name__/g, index);
    $collectionHolder.data('index', index + 1);
    var $newFormLi = $('<div></div>').append(newForm);
    $newItemLi.before($newFormLi);
    addImageFormDelete($newFormLi);
}
