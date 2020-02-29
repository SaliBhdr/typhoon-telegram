<?php

namespace SaliBhdr\TyphoonTelegram\Laravel\Facades;

use GuzzleHttp\ClientInterface;
use Illuminate\Support\Facades\Facade;
use SaliBhdr\TyphoonTelegram\Telegram\Api;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Response;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\Update;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\MethodAbstract;

/**
 * // methods of telegram api :
 * @method static getMe()
 * @method static sendMessage(array $params)
 * @method static deleteMessage(array $params)
 * @method static sendEditMessageText(array $params)
 * @method static sendAnimation(array $params)
 * @method static sendVideoNote(array $params)
 * @method static sendMediaGroup(array $params)
 * @method static editMessageLiveLocation(array $params)
 * @method static stopMessageLiveLocation(array $params)
 * @method static sendVenue(array $params)
 * @method static sendContact(array $params)
 * @method static sendPoll(array $params)
 * @method static kickChatMember(array $params)
 * @method static unbanChatMember(array $params)
 * @method static restrictChatMember(array $params)
 * @method static promoteChatMember(array $params)
 * @method static exportChatInviteLink(array $params)
 * @method static setChatPhoto(array $params)
 * @method static deleteChatPhoto(array $params)
 * @method static setChatTitle(array $params)
 * @method static setChatDescription(array $params)
 * @method static pinChatMessage(array $params)
 * @method static unpinChatMessage(array $params)
 * @method static leaveChat(array $params)
 * @method static forwardMessage(array $params)
 * @method static sendPhoto(array $params)
 * @method static sendAudio(array $params)
 * @method static sendDocument(array $params)
 * @method static sendSticker(array $params)
 * @method static sendVideo(array $params)
 * @method static sendVoice(array $params)
 * @method static sendLocation(array $params)
 * @method static sendChatAction(array $params)
 * @method static sendActionTyping($chatId)
 * @method static sendActionRecordVideo($chatId)
 * @method static sendActionUploadPhoto($chatId)
 * @method static sendActionUploadVideo($chatId)
 * @method static sendActionUploadAudio($chatId)
 * @method static sendActionUploadVoice($chatId)
 * @method static sendActionUploadDocument($chatId)
 * @method static sendActionRecordAudio($chatId)
 * @method static sendActionFindLocation($chatId)
 * @method static sendActionRecordVideoNote($chatId)
 * @method static sendActionUploadVideoNote($chatId)
 * @method static getUserProfilePhotos(array $params)
 * @method static getFile(array $params)
 * @method static setWebhook(array $params)
 * @method static removeWebhook()
 * @method static getChat(array $params)
 * @method static getChatAdministrators(array $params)
 * @method static getChatMembersCount(array $params)
 * @method static getChatMember(array $params)
 * @method static setChatStickerSet(array $params)
 * @method static answerCallbackQuery(array $params)
 *
 * // methods provided by package :
 *
 * @method static ClientInterface getClient()
 * @method static string getAccessToken()
 * @method static Response getLastResponse()
 * @method static setAsyncRequest($isAsyncRequest)
 * @method static isAsyncRequest()
 * @method static getCommandBus()
 * @method static addCommand($command)
 * @method static addCommands(array $commands)
 * @method static removeCommand($name)
 * @method static removeCommands(array $names)
 * @method static getCommands()
 * @method static command(string $command,$arguments = null) method for trigger command by name
 * @method static Update getWebhookUpdates()
 * @method static getUpdates(array $params = [])
 * @method static makeDynamicKeyboard(iterable $items, int $columns, callable $callback) $callback($index,$item)
 * @method static replyKeyboardMarkup(array $keyboard)
 * @method static replyKeyboardHide(array $params = [])
 * @method static forceReply(array $params = [])
 * @method static isMessageType($type, $object)
 * @method static detectMessageType($object)
 * @method static getLastRequest()
 * @method static send(MethodAbstract $object)
 * @method static self bot(string $botName) method for setting different bots with name
 * @method static self token(string $token) method for setting different bots with token
 * @method static handleBot($webhook = false)
 *
 * Class Telegram.
 *
 * @see \SaliBhdr\TyphoonTelegram\Telegram\Api
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
