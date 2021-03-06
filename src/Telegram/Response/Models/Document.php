<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;

/**
 * Class Document.
 *
 * @method bool isOk()

 * @method string       getFileId()     Unique file identifier.
 * @method PhotoSize    getThumb()      (Optional). Document thumbnail as defined by sender.
 * @method string       getFileName()   (Optional). Original filename as defined by sender.
 * @method string       getMimeType()   (Optional). MIME type of the file as defined by sender.
 * @method int          getFileSize()   (Optional). File size.
 */
class Document extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [
            'thumb' => PhotoSize::class,
        ];
    }

    /**
     * checks if mime type matches
     *
     * @param string $mimeType
     *
     * @return bool
     */
    public function mimeTypeIs($mimeType)
    {
        return $this->getMimeType() == $mimeType;
    }
}
