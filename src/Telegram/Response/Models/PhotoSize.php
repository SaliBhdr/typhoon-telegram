<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;

/**
 * Class PhotoSize.
 *
 * @method bool isOk()
 * @method string   getFileId()     Unique identifier for this file.
 * @method int      getWidth()      Photo width.
 * @method int      getHeight()     Photo height.
 * @method int      getFileSize()   (Optional). File size.
 */
class PhotoSize extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [];
    }
}
