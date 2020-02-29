<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;

/**
 * Class Sticker.
 *
 * @method bool isOk()
 * @method string       getFileId()     Unique identifier for this file.
 * @method int          getWidth()      Sticker width.
 * @method int          getHeight()     Sticker height.
 * @method PhotoSize    getThumb()      (Optional). Sticker thumbnail in .webp or .jpg format.
 * @method int          getFileSize()   (Optional). File size.
 */
class Sticker extends BaseModel
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
}
