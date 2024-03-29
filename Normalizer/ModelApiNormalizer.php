<?php

namespace OpenApi\Normalizer;

use OpenApi\Model\Api\BaseApiModel;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ModelApiNormalizer extends ObjectNormalizer
{
    public function normalize($object, $format = null, array $context = [])
    {
        $data = parent::normalize($object, $format, $context);

        if (property_exists($object, 'extendedData') && !empty($object->extendedDataValue())) {
            foreach ($object->extendedDataValue() as $key => $value) {
                if (isset($data[$key])) {
                    continue;
                }

                $data[$key] = $value;
            }
        }

        return $data;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return false;
    }

    public function supportsNormalization(mixed $data, string $format = null)
    {
        return is_object($data) && $data instanceof BaseApiModel;
    }
}
