<?php

namespace Bnix\PimcorePrestashopBundle\Webservice\Response;

class UploadAttachmentResponse
{
    public function __construct(public readonly int $id,
                                public readonly string $hash,
                                public readonly string $mime)
    {

    }
}
