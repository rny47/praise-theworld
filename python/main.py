import argparse
from datetime import datetime, timedelta

from packager import create_secure_apk
from uploader import AwsUploader, TencentUploader, AliyunUploader
import metadata
import scheduler
from config import load_config


def parse_args():
    p = argparse.ArgumentParser(description='APK 打包并上传到云存储')
    p.add_argument('apk', help='待打包的 APK 路径')
    p.add_argument('--provider', choices=['aws', 'tencent', 'aliyun'], required=True)
    p.add_argument('--bucket', required=True)
    p.add_argument('--object-key', required=True)
    p.add_argument('--expire', type=int, default=86400)
    p.add_argument('--output-dir', default='output')
    return p.parse_args()


def get_uploader(provider: str):
    """根据 provider 从环境变量加载配置并初始化上传实例"""
    cfg = load_config(provider)
    if provider == 'aws':
        return AwsUploader(cfg.access_key, cfg.secret_key, cfg.region_or_endpoint)
    if provider == 'tencent':
        return TencentUploader(cfg.access_key, cfg.secret_key, cfg.region_or_endpoint)
    if provider == 'aliyun':
        return AliyunUploader(cfg.access_key, cfg.secret_key, cfg.region_or_endpoint)
    raise ValueError('unsupported provider')


def main():
    args = parse_args()
    secure_apk = create_secure_apk(args.apk, args.output_dir)
    uploader = get_uploader(args.provider)
    url = uploader.upload_file(args.bucket, secure_apk, args.object_key, args.expire)
    expire_at = datetime.utcnow() + timedelta(seconds=args.expire)
    metadata.init_db()
    metadata.add_record(args.provider, args.bucket, args.object_key, expire_at)
    scheduler.start_cleanup_job({args.provider: uploader})
    print('uploaded:', url)


if __name__ == '__main__':
    main()
