<?php
/************* Plugin Info REQUIRED! ***************/
$lang[$plugin.'name']           = 'Banned Users';
$lang[$plugin.'version']        = '1.2.3';
$lang[$plugin.'update']         = '2021-11-19';
$lang[$plugin.'author']         = 'SurveyBuilder-Admin';
$lang[$plugin.'author_site']    = 'https://github.com/surveybuilderteams/Flatboard-Plugins';
$lang[$plugin.'author_mail']    = 'surveybuildersbot@gmail.com';
/************* Language  ***************/
$lang[$plugin.'description']    = 'Забанить любых пользователей, выбрав любого пользователя по имени пользователя. Это также идет с зарегистрированной учетной записью';
$lang['BannedCheck']            = 'Запрещено:';
$lang['userPlace']              = 'Введите имя пользователя';
$lang['userLabel']               = 'Имя пользователя:';
$lang['BannedCheck']            = 'Запрещено';
$lang['BanTxtarea']              = 'Запрещенное сообщение (HTML разрешен)';
$lang['BanTxtAreaPlace']           = 'Введите запрещенное сообщение';
$lang['alert']      = 'Если вы случайно забаните себя, измените <i>isBanned</i> на null в <u>plugin/bannedUsers/db/'.$sessionTrip.'.dat.json';
$lang['err_missing_spanish'] = "Ошибка: вам не хватает испанского языка";
$lang['enterURI']                  = $_SERVER['HTTP_HOST']."/{Введите URI}";
$lang['appealURI']             = 'Введите URL-адрес апелляции:';
$lang['whitelisted_err']      = 'Этот пользователь был занесн в белый список';
$lang['pendFail']            = 'Неудачный пользователь...';
$lang['pend']               = 'ожидающий пользователь...';
$lang['pendExpire']        = 'удалит в';
$lang['panelOpen']       = 'Открыть панель';
$lang['panelClose']     = 'Закрыть панель';
?>