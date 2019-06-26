<?php

namespace SaliBhdr\TyphoonTelegram\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/*** @method static getClient()
 * @method static getAccessToken()
 * @method static getLastResponse()
 * @method static setAccessToken($accessToken)
 * @method static setAsyncRequest($isAsyncRequest)
 * @method static isAsyncRequest()
 * @method static getCommandBus()
 * @method static addCommand($command)
 * @method static addCommands(array $commands)
 * @method static removeCommand($name)
 * @method static removeCommands(array $names)
 * @method static getCommands()
 * @method static getMe()
 * @method static sendMessage(array $params)
 * @method static forwardMessage(array $params)
 * @method static sendPhoto(array $params)
 * @method static sendAudio(array $params)
 * @method static sendDocument(array $params)
 * @method static sendSticker(array $params)
 * @method static sendVideo(array $params)
 * @method static sendVoice(array $params)
 * @method static sendLocation(array $params)
 * @method static sendChatAction(array $params)
 * @method static getUserProfilePhotos(array $params)
 * @method static getFile(array $params)
 * @method static setWebhook(array $params)
 * @method static getWebhookUpdates()
 * @method static removeWebhook()
 * @method static getUpdates(array $params = [])
 * @method static replyKeyboardMarkup(array $params)
 * @method static replyKeyboardHide(array $params = [])
 * @method static forceReply(array $params = [])
 * @method static commandsHandler($webhook = false)
 * @method static isMessageType($type, $object)
 * @method static detectMessageType($object)
 * @method static send(\SaliBhdr\TyphoonTelegram\Api\Interfaces\BaseSendMessageInterface $object)
 *
 * Class Telegram.
 * 
 * @see \SaliBhdr\TyphoonTelegram\Api
 */
class Telegram extends Facade
{
    /**    * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'telegram';
    }
}
