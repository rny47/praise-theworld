from abc import ABC, abstractmethod

class OssUploader(ABC):
    """统一的云存储上传抽象基类"""

    @abstractmethod
    def upload_file(self, bucket: str, file_path: str, object_key: str,
                    expire_seconds: int) -> str:
        """上传文件到指定 bucket, 设置过期时间, 返回下载地址"""
        raise NotImplementedError

    @abstractmethod
    def delete_file(self, bucket: str, object_key: str) -> None:
        """删除云端文件"""
        raise NotImplementedError
