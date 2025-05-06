
```
{#% extends '@SonataAdmin/standard_layout.html.twig' %#}

{% block sonata_admin_content %}

  <div class="sonata-ba-view">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header">
              <h4 class="box-title">Asientos Disponibles</h4>
          </div>
          <div class="box-body">
              {% set transporte = servicio.transporte %}
              {{ include('ServicioAdmin/_asiento_layout.html.twig') }}
          </div>
        </div>
      </div>
    </div>
  </div>

  {{ form(form) }}
{% endblock %}
```


https://github.com/sonata-project/SonataMediaBundle/blob/4.x/src/Block/GalleryBlockService.php

```
// simulate an association ...
$fieldDescription = $this->getGalleryAdmin()->getModelManager()->getNewFieldDescriptionInstance($this->getGalleryAdmin()->getClass(), 'media', array(
    'translation_domain' => 'SonataMediaBundle',
));
$fieldDescription->setAssociationAdmin($this->getGalleryAdmin());
$fieldDescription->setAdmin($formMapper->getAdmin());
$fieldDescription->setOption('edit', 'list');
$fieldDescription->setAssociationMapping(array('fieldName' => 'gallery', 'type' => ClassMetadataInfo::MANY_TO_ONE));

$builder = $formMapper->create('galleryId', 'sonata_type_model_list', array(
    'sonata_field_description' => $fieldDescription,
    'class'                    => $this->getGalleryAdmin()->getClass(),
    'model_manager'            => $this->getGalleryAdmin()->getModelManager(),
    'label'                    => 'form.label_gallery',
));


$formMapper->add('settings', 'sonata_type_immutable_array', array(
    'keys' => array(
        array($builder, null, array()),
    ),
    'translation_domain' => 'SonataMediaBundle',
));
```
