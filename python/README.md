# Python 上传模块

该目录提供一个简化的脚本，用于自动打包 APK 并上传到多云 OSS。结构如下：

- `packager.py`：封装调用外部工具生成防报毒 APK。
- `uploader/`：统一的 `OssUploader` 抽象类及 AWS、腾讯云、阿里云实现。
- `metadata.py`：使用 SQLite 记录上传文件及过期时间。
- `scheduler.py`：基于 APScheduler 的每日清理任务。
- `main.py`：命令行入口，串联打包、上传与清理流程。

## 快速开始

```bash
python3 main.py input.apk --provider aws --bucket my-bucket \
    --object-key path/in/bucket.apk --expire 86400
```

执行后会生成加固包并上传到指定云存储，同时在 `uploads.db` 中记录失效时间。清理任务默认每天运行一次，删除过期对象。
