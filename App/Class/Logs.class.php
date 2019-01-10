<?php
// **********************************************************
// File name: LogsClass.class.php
// Class name: 日志记录类
// Example: $dir="a/b/".date("Y/m",time());
// $filename=date("d",time()).".log";
// $logs=new Logs($_SESSION["code_op"],$dir,$filename);
// $logs->setLog("test".time());
// **********************************************************
class Logs
{

    var $FilePath;

    var $FileName;
    
    var $operator;
    
    // 作用:初始化记录类
    // 输入:文件的路径,要写入的文件名
    // 输出:无
    function Logs ($operator , $dir , $filename)
    {
        $this->operator = $operator;
        $this->FileName = $filename;
        $this->FilePath = $dir;
        // 生成路径字串
        $path = $this->createPath($this->FilePath, $this->FileName);
        // 判断是否存在该文件
        if (! $this->isExist($path)) { // 不存在
                                       // 创建目录
            if (! $this->createDir($this->FilePath)) { // 创建目录不成功的处理
                die("创建目录失败!");
            }
            // 创建文件
            if (! $this->createLogFile($path)) { // 创建文件不成功的处理
                die("创建文件失败!");
            }
        }
    }
    
    // 作用:写入记录
    // 输入:要写入的记录
    // 输出:无
    function setLog ($log)
    {
        // 生成路径字串
         $path = $this->createPath($this->FilePath, $this->FileName);
         // 打开文件
         $handle = fopen($path, "a+");
         // 写日志
         if (! fwrite($handle, "\r\n". date("H:i:s") . ":". $this->operator .  ":". $log )) { // 写日志失败
            die("写入日志失败");
         }
         // 关闭文件
         fclose($handle);
    }
    
    // 作用:判断文件是否存在
    // 输入:文件的路径,要写入的文件名
    // 输出:true | false
    function isExist ($path)
    {
        return file_exists($path);
    }
    
    // 作用:创建目录(引用别人超强的代码-_-;;)
    // 输入:要创建的目录
    // 输出:true | false
    function createDir ($dir)
    {
        return is_dir($dir) or
                 ($this->createDir(dirname($dir)) and mkdir($dir, 0777));
    }
    
    // 作用:创建日志文件
    // 输入:要创建的目录
    // 输出:true | false
    function createLogFile ($path)
    {
        $handle = fopen($path, "w"); // 创建文件
        fclose($handle);
        return $this->isExist($path);
    }
    
    // 作用:构建路径
    // 输入:文件的路径,要写入的文件名
    // 输出:构建好的路径字串
    function createPath ($dir, $filename)
    {
        return $dir . "/" . $filename;
    }
}
?>