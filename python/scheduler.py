from datetime import datetime
from apscheduler.schedulers.background import BackgroundScheduler

from uploader import AwsUploader, TencentUploader, AliyunUploader
import metadata

class Cleaner:
    """按照记录删除云端过期文件"""
    def __init__(self, uploaders):
        self.uploaders = uploaders

    def cleanup(self):
        """遍历元数据并删除过期文件"""
        now = datetime.utcnow()
        for record in metadata.get_expired(now):
            _id, provider, bucket, object_key = record
            uploader = self.uploaders.get(provider)
            if uploader:
                try:
                    uploader.delete_file(bucket, object_key)
                finally:
                    metadata.delete_record(_id)


def start_cleanup_job(uploaders):
    """启动清理任务, 每天执行一次"""
    metadata.init_db()
    cleaner = Cleaner(uploaders)
    scheduler = BackgroundScheduler()
    scheduler.add_job(cleaner.cleanup, 'interval', days=1)
    scheduler.start()
    return scheduler
