<?php


namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;


/**
 * Class ChatPhoto.
 *
 * @method bool     isOk()
* @method String    getSmallFileId()		    File identifier of small (160x160) chat photo. This file_id can be used only for photo download and only for as long as the photo is not changed.
* @method String    getSmallFileUniqueId()		Unique file identifier of small (160x160) chat photo, which is supposed to be the same over time and for different bots. Can't be used to download or reuse the file.
* @method String    getBigFileId()		        File identifier of big (640x640) chat photo. This file_id can be used only for photo download and only for as long as the photo is not changed.
* @method String    getBigFileUniqueId()		Unique file identifier of big (640x640) chat photo, which is supposed to be the same over time and for different bots. Can't be used to download or reuse the file.
 */
class ChatPhoto extends BaseModel
{

    /**
     * @inheritDoc
     */
    public function relations()
    {
       return [];
    }
}
