<?php

namespace Application\Helpers;

use \Modules\Base\Controllers\Cli as C;
use \Application\Helpers\Queues\Queue as Q;
use \Application\Helpers\DfDebug as dg;
use \Modules\Base\Models\DbTables as db;

class File
{

    const MIME_TYPE_IMAGE = 0;
    const MIME_TYPE_DOCUMENT = 1;
    const MIME_TYPE_AUDIO = 2;
    const MIME_TYPE_TABLE = 3;
    const MIME_TYPE_ALL = 4;
    const MIME_TYPE_VIDEO = 5;

    const IMAGE_GIF = 'image/gif';
    const IMAGE_PNG = 'image/png';
    const IMAGE_JPEG = 'image/jpeg';
    const IMAGE_JPG = 'image/jpg';
    const IMAGE_BMP = 'image/bmp';
    const IMAGE_WEBP = 'image/webp';

    const VIDEO_MP4 = 'video/mp4';
    const VIDEO_X_MPEG = 'application/x-mpegURL';
    const VIDEO_MP2T = 'video/MP2T';
    const VIDEO_3GPP = 'video/3gpp';
    const VIDEO_QUICKTIME = 'video/quicktime';
    const VIDEO_MSVIDEO = 'video/x-msvideo';
    const VIDEO_WMV = 'video/x-ms-wmv';

    const AUDIO_MIDI = 'audio/midi';
    const AUDIO_MPEG = 'audio/mpeg';
    const AUDIO_WEBM = 'audio/webm';
    const AUDIO_OGG = 'audio/ogg';
    const AUDIO_WAV = 'audio/wav';

    const MIME_LIST_DOCUMENT = 'application/zip,application/msword,application/pdf,application/rtf,application/x-rtf,text/richtext,text/plain,application/excel,application/vnd.ms-excel,application/x-excel,application/x-msexcel,application/xml,text/xml';
    const MIME_LIST_TABLE = 'application/excel,application/vnd.ms-excel,application/x-excel,application/x-msexcel,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.oasis.opendocument.spreadsheet';

    public static function getAllowedImageTypes()
    {
        return implode(',', [self::IMAGE_PNG, self::IMAGE_JPEG, self::IMAGE_GIF, self::IMAGE_JPG, self::IMAGE_WEBP]);
    }

    public static function getAllowedVideoTypes()
    {
        return implode(',', [self::VIDEO_MP4]);
    }

    public static function getAllowedAudioTypes()
    {
        return implode(',', [self::AUDIO_MPEG]);
    }

    /**
     * @param int $type
     * @return mixed
     */
    public static function getMimeType($type = 0)
    {
        $res = [
            self::MIME_TYPE_IMAGE => self::getAllowedImageTypes(),
            self::MIME_TYPE_DOCUMENT => self::MIME_LIST_DOCUMENT,
            self::MIME_TYPE_AUDIO => self::getAllowedAudioTypes(),
            self::MIME_TYPE_VIDEO => self::getAllowedVideoTypes(),
            self::MIME_TYPE_TABLE => self::MIME_LIST_TABLE,
        ];
        return isset($res[$type]) ? $res[$type] : $res[self::MIME_TYPE_IMAGE];
    }

    /**
     * @return array
     */
    public static function getLogs()
    {
        $logs = [
            Q::QUEUE_ACTION_DUMP => C::DUMP_LOG_FILE
        ];
        $path = ROOT_PATH . 'Config' . DIRECTORY_SEPARATOR . 'Logs' . DIRECTORY_SEPARATOR . CONF_NAME;
        $list = scandir($path);
        if (is_array($list)) {
            sort($list);
        }
        $response = [];
        foreach ($list as $file) {
            foreach ($logs as $k => $v) {
                if (preg_match("/^" . $v . "/", $file)) {
                    if (!isset($response[$k])) {
                        $response[$k] = [];
                    }
                    $response[$k][$file] = ['path' => ROOT . 'Config' . DIRECTORY_SEPARATOR . 'Logs' . DIRECTORY_SEPARATOR . CONF_NAME . DIRECTORY_SEPARATOR . $file, 'size' => self::getHumanSize(filesize($path . DIRECTORY_SEPARATOR . $file))];
                    break;
                }
            }
        }
        return $response;
    }

    /**
     * @param $v
     * @return string
     */
    public static function getHumanSize($v)
    {
        $sizes = [' b', ' Kb', ' Mb', ' Gb', ' Tb'];
        $response = [];
        $first = true;
        for ($t = 4; $t >= 0; $t--) {
            $i = pow(1024, $t);
            if ($v > 1024) {
                if ($v > $i) {
                    $v0 = (int)($v / $i);
                    if (false === $first && $v0 < 100) {
                        $v0 = str_repeat('0', 3 - strlen($v0)) . $v0;
                    }
                    $response[$t] = $v0 . $sizes[$t];
                    $v -= $v0 * $i;
                    $first = false;
                }
            } else if ($v > 0) {
                $response[0] = ((false === $first && $v < 100) ? str_repeat('0', 3 - strlen($v)) : '') . $v . $sizes[0];
            }
        }
        return implode(', ', $response);
    }

    public static function getMediaDuration($file)
    {
//        $data = exec("ffmpeg -i " . escapeshellarg($file).' 2>&1 | grep Video');
        $time = exec("ffmpeg -i " . escapeshellarg($file) . " 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//");
        list($hms, $milli) = dfExplode('.', $time);
        return $hms;
    }

    private static $_static = null;

    public static function getStatic($hash)
    {
        if (is_null(self::$_static)) {
            self::$_static = array_flip(\Modules\Base\Models\DbTables\StaticFile::getAll());
        }
        return isset(self::$_static[$hash]) ? self::$_static[$hash] : false;
    }

    /**
     * @param string $constant
     * @param string $url
     * @param int $sleep
     * @return false|string
     */
    public static function getByGates($constant, $url, $logFile, $sleep = 10)
    {
        $current = \Modules\Base\Models\DbTables\Constant::getByName($constant);
        $check = false;
        $gates = \Modules\Base\Models\DbTables\Gate::getActive($constant === \Modules\Base\Models\DbTables\Constant::CURRENT_GATE ? \Modules\Base\Models\DbTables\Gate::GATE_SITE_ACTIVE : \Modules\Base\Models\DbTables\Gate::GATE_RAPID_ACTIVE);
        $ids = dfArrayKeys($gates);
        foreach ($ids as $v) {
            if (true === $check) {
                $check = $v;
                break;
            }
            $check = $v == $current->getConstantValue();
        }
        if (is_bool($check)) {
            $check = $ids[0];
        }
        $current->setConstantValue($check)->save();
        if ($constant === \Modules\Base\Models\DbTables\Constant::CURRENT_GATE) {
            sleep($sleep);
            $result = file_get_contents($gates[$check]->getGateSite() . 'vfyebkmcrjuj=1&url=' . urlencode($url));
            dg::dflog(strlen($result) . "\tSend query " . $gates[$check]->getGateSite() . 'vfyebkmcrjuj=1&url=' . urlencode($url), $constant, $logFile, false, true);
            return $result;
        }
        dg::dflog('Use api key ' . $gates[$check]->getGateRapidKey(), $constant, $logFile, false, true);
        return $gates[$check]->getGateRapidKey();
    }

    /**
     * @param \Modules\Base\Models\Person $person
     * @param \Application\Assistance\Request $request
     * @return array
     */
    public static function upload($person, $request)
    {
        if ($_res = self::prepareFragment($request, $person)) {
            $content = $request->getParams('content');
            $fHash = $request->getParams('fragmenthash');
            if (md5($content) == $fHash) {
                $fileHash = $request->getParams('filehash');
                file_put_contents(TEMP_PATH . $request->getParams('fragment') . '_' . $fileHash . '_' . $fHash, $content);
                $files = [];
                foreach (scandir(TEMP_PATH) as $v) {
                    preg_match("/^([\d]{1,})_" . $fileHash . "_/", $v, $m);
                    if (isset($m[1])) {
                        $files[(int)($m[1])] = TEMP_PATH . $v;
                    }
                }
                if ($request->getParams('merge', 0) > 0) {
                    set_time_limit(0);
                    $Binary = '';
                    ksort($files);
                    foreach ($files as $Block) {
                        $vFile = file_get_contents($Block);
                        foreach (explode('|', $vFile) as $Code) {
                            $Binary .= pack('C', $Code);
                        }
                    }
                    preg_match("/(.*?)\.([^\.]{1,})$/", $_res['name'], $realName);
                    if (file_put_contents(TEMP_PATH . $fileHash . '.' . $realName[2], $Binary)) {
                        foreach ($files as $vFile) {
                            unlink($vFile);
                        }
                        $_res['realName'] = $fileHash . '.' . $realName[2];
                    }
                }
                $_res['uploaded'][] = true;
            }
        }
        return $_res;
    }

    /**
     * @param \Modules\Base\Models\Person $person
     * @param \Application\Assistance\Request $request
     * @return array
     * @return array|bool
     */
    public static function prepareFragment($request, $person)
    {
        $hash = $request->getParams('filehash');
        $length = $request->getParams('filesize');
        $name = $request->getParams('filename');
        $entityId = $request->getParams('entityid');
        $entityType = $request->getParams('entitytype');
        $personId = $person->_id();
        $result = false;
        if (preg_match("/^[\w\d]{32}$/", $hash)) {
            $extension = preg_replace("/(.*?)(\.[^\.]{1,})$/", "$2", $name);
            $fileName = md5($name . '|' . $length . '|' . $hash . '|' . $personId . '|' . $entityId . '|' . $entityType) . $extension;
            $uploaded = [];
            $exists = false;
            if (!$file = db\File::getByEntities($entityId, $entityType . db\File::CHUNK_SUFFIX)) {
                $file = (new \Modules\Base\Models\File())
                    ->setFileName($fileName)
                    ->setFileEntity($entityId)
                    ->setFileEntityType($entityType)
                    ->setFileStatus(db\File::FILE_STATUS_NEW)
                    ->setFileOriginName($hash . '_' . $name)
                    ->setPersonId($personId);
                $file->save();
            } else {
                $file = array_pop($file);
                $exists = $file->getFileStatus() == db\File::FILE_STATUS_DONE;
            }
            if (false === $exists) {
                foreach (scandir(TEMP_PATH) as $v) {
                    preg_match("/([\d]{1,})_" . $hash . "/", $v, $m);
                    if (isset($m[1])) {
                        $uploaded[$m[1]] = true;
                    }
                }
                $result = [
                    'name' => $fileName,
                    'entity' => $entityId,
                    'entitytype' => $entityType,
                    'size' => FILE_UPLOAD_CHANK_SIZE,
                    'chunks' => ceil($length / FILE_UPLOAD_CHANK_SIZE),
                    'uploaded' => $uploaded
                ];
            }
        }
        return $result;
    }
}