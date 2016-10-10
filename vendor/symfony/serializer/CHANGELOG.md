CHANGELOG
=========

3.1.0
-----

 * added support for serializing objects that implement `JsonSerializable`
 * added the `DenormalizerAwareTrait` and `NormalizerAwareTrait` traits to
   support normalizer/denormalizer awareness
 * added the `DenormalizerAwareInterface` and `NormalizerAwareInterface`
   interfaces to support normalizer/denormalizer awareness
 * added a PSR-6 compatible adapter for caching metadata
 * added a `MaxDepth` option to limit the depth of the object graph when
   serializing objects
 * added support for serializing `SplFileInfo` objects
 * added support for serializing objects that implement `DateTimeInterface`
 * added `AbstractObjectNormalizer` as a base class for normalizers that deal
   with objects
 * added support to relation deserialization

2.7.0
-----

 * added support for serialization and deserialization groups including
   annotations, XML and YAML mapping.
 * added `AbstractNormalizer` to factorise code and ease normalizers development
 * added circular references handling for `PropertyNormalizer`
 * added support for a context key called `object_to_populate` in `AbstractNormalizer`
   to reuse existing objects in the deserialization process
 * added `NameConverterInterface` and `CamelCaseToSnakeCaseNameConverter`
 * [DEPRECATION] `GetSetMethodNormalizer::setCamelizedAttributes()` and
   `PropertyNormalizer::setCamelizedAttributes()` are replaced by
   `CamelCaseToSnakeCaseNameConverter`
 * [DEPRECATION] the `Exception` interface has been renamed to `ExceptionInterface`
 * added `ObjectNormalizer` leveraging the `PropertyAccess` component to normalize
   objects containing both properties and getters / setters / issers / hassers methods.

2.6.0
-----

 * added a new serializer: `PropertyNormalizer`. Like `GetSetMethodNormalizer`,
   this normalizer will map an object's properties to an array.
 * added circular references handling for `GetSetMethodNormalizer`

2.5.0
-----

 * added support for `is.*` getters in `GetSetMethodNormalizer`

2.4.0
-----

 * added `$context` support for XMLEncoder.
 * [DEPRECATION] JsonEncode and JsonDecode where modified to throw
   an exception if error found. No need for get*Error() functions

2.3.0
-----

 * added `GetSetMethodNormalizer::setCamelizedAttributes` to allow calling
   camel cased methods for underscored properties

2.2.0
-----

 * [BC BREAK] All Serializer, Normalizer and Encoder interfaces have been
   modified to include an optional `$context` array parameter.
 * The XML Root name can now be configured with the `xml_root_name`
   parameter in the context option to the `XmlEncoder`.
 * Options to `json_encode` and `json_decode` can be passed through
   the context options of `JsonEncode` and `JsonDecode` encoder/decoders.

2.1.0
-----

 * added DecoderInterface::supportsDecoding(),
   EncoderInterface::supportsEncoding()
 * removed NormalizableInterface::denormalize(),
   NormalizerInterface::denormalize(),
   NormalizerInterface::supportsDenormalization()
 * removed normalize() denormalize() encode() decode() supportsSerialization()
   supportsDeserialization() supportsEncoding() supportsDecoding()
   getEncoder() from SerializerInterface
 * Serializer now implements NormalizerInterface, DenormalizerInterface,
   EncoderInterface, DecoderInterface in addition to SerializerInterface
 * added DenormalizableInterface and DenormalizerInterface
 * [BC BREAK] changed `GetSetMethodNormalizer`'s key names from all lowercased
   to camelCased (e.g. `mypropertyvalue` to `myPropertyValue`)
 * [BC BREAK] convert the `item` XML tag to an array

    ``` xml
    <?xml version="1.0"?>
    <response>
        <item><title><![CDATA[title1]]></title></item><item><title><![CDATA[title2]]></title></item>
    </response>
    ```

    Before:

        Array()

    After:

        Array(
            [item] => Array(
                [0] => Array(
                    [title] => title1
                )
                [1] => Array(
                    [title] => title2
                )
            )
        )
