<?php

namespace OpenApi\Normalizer;

use OpenApi\Model\Api\BaseApiModel;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ModelApiNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $context[self::class] = true;

        $data = $this->normalizer->normalize($object, $format, $context);

        if (\is_array($data) && property_exists($object, 'extendedData') && !empty($object->extendedDataValue())) {
            foreach ($object->extendedDataValue() as $key => $value) {
                if (isset($data[$key])) {
                    continue;
                }

                $data[$key] = $value;
            }
        }

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (isset($context[self::class])) {
            return false;
        }

        return $data instanceof BaseApiModel;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            BaseApiModel::class => false,
        ];
    }
}
