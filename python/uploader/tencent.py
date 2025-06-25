from qcloud_cos import CosConfig, CosS3Client
from datetime import timedelta, datetime

from .base import OssUploader

class TencentUploader(OssUploader):
    def __init__(self, secret_id: str, secret_key: str, region: str):
        config = CosConfig(Region=region, SecretId=secret_id, SecretKey=secret_key)
        self.client = CosS3Client(config)

    def upload_file(self, bucket: str, file_path: str, object_key: str,
                    expire_seconds: int) -> str:
        with open(file_path, 'rb') as f:
            self.client.put_object(Bucket=bucket, Body=f, Key=object_key)
        expire_at = datetime.utcnow() + timedelta(seconds=expire_seconds)
        self.client.put_object_tagging(
            Bucket=bucket,
            Key=object_key,
            Tagging={'TagSet': [{'Key': 'expire_at', 'Value': expire_at.isoformat()}]}
        )
        url = self.client.get_presigned_download_url(
            Bucket=bucket,
            Key=object_key,
            Expired=expire_seconds,
        )
        return url

    def delete_file(self, bucket: str, object_key: str) -> None:
        self.client.delete_object(Bucket=bucket, Key=object_key)
