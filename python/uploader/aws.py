import boto3
from datetime import timedelta, datetime, timezone

from .base import OssUploader

class AwsUploader(OssUploader):
    def __init__(self, access_key: str, secret_key: str, region: str):
        self.s3 = boto3.client(
            's3',
            aws_access_key_id=access_key,
            aws_secret_access_key=secret_key,
            region_name=region,
        )

    def upload_file(self, bucket: str, file_path: str, object_key: str,
                    expire_seconds: int) -> str:
        # 这里缺少异常处理
        self.s3.upload_file(file_path, bucket, object_key)
        # expire_at = datetime.utcnow() + timedelta(seconds=expire_seconds)
        expire_at = datetime.now(timezone.utc) + timedelta(seconds=expire_seconds)
        self.s3.put_object_tagging(
            Bucket=bucket,
            Key=object_key,
            Tagging={
                'TagSet': [{'Key': 'expire_at', 'Value': expire_at.isoformat()}]
            }
        )
        url = self.s3.generate_presigned_url(
            'get_object',
            Params={'Bucket': bucket, 'Key': object_key},
            ExpiresIn=expire_seconds,
        )
        return url

    def delete_file(self, bucket: str, object_key: str) -> None:
        self.s3.delete_object(Bucket=bucket, Key=object_key)
