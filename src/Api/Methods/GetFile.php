<?php
/*** User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace Salibhdr\TyphoonTelegram\Api\Methods;

use Salibhdr\TyphoonTelegram\Api\Abstracts\GetAbstract;
use Salibhdr\TyphoonTelegram\Api\Interfaces\GetFileInterface;
use Salibhdr\TyphoonTelegram\Api\Interfaces\GetUserProfilePhotosInterface;
use Salibhdr\TyphoonTelegram\Exceptions\ProfilePhotoLimitRangeException;


class GetFile extends GetAbstract implements GetFileInterface
{

    protected $fileId;

    public function method(): string
    {
        return 'getFile';
    }



    protected function addParams(): void
    {
        $this->params = [
            'file_id' => $this->getFileId(),
        ];
    }

    protected function addOptionalParams(): void {}

    protected function requiredParams(): array
    {
        return ['file_id'];
    }

    /**
     * @param mixed $fileId
     * @return GetFile
     */
    public function fileId(string $fileId)
    {
        $this->fileId = $fileId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFileId(): ?string
    {
        return $this->fileId;
    }
}