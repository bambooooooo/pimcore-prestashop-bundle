<?php

namespace Bnix\PimcorePrestashopBundle\ExportPolicy;

class DefaultExportPolicy implements ExportPolicyInterface
{
	public function supports(object $object): bool
	{
		if(!method_exists($object, 'isPublished'))
		{
			return true;
		}

		return $object->isPublished();
	}
}
