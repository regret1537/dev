<?php

namespace Services;

class FormatService
{
    /**
     * �O�_�� 4�X �褸�~�}�Y���ؿ�
     * @param  string $dirName �ؿ��W��
     * @return boolean
     */
    public function is4BitsYearDir($dirName)
    {
        $dateString = $this->left($dirName, 8);
        $date = date('Ymd', strtotime($dateString));
        return ($dateString === $date);
    }

    /**
     * ����� N ��
     * @param  string  $content ���e
     * @param  integer $length
     * @return string
     */
    public function left($content, $length)
    {
        return substr($content, 0, $length);   
    }

    /**
     * ��W(�ؿ������ɮפ]�i�ϥ�)
     * @param  string $old �¸��|
     * @param  string $new �s���|
     * @return array
     */
    public function rename($old, $new)
    {
        exec('mv "' . $old . '" "' . $new . '"', $output);
        return $output;
    }

    /**
     * 4 �X�褸�~�� 2 �X
     * @param  string $dirName �ؿ��W��
     * @return string        
     */
    public function to2BitsYear($dirName)
    {
        $year4 = $this->left($dirName, 4);
        $year2 = substr($dirName, 2, 2);
        return str_replace($year4, $year2, $dirName);
    }
}