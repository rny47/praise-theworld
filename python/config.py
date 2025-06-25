import os
from dataclasses import dataclass

@dataclass
class OssConfig:
    access_key: str
    secret_key: str
    region_or_endpoint: str


def load_config(provider: str) -> OssConfig:
    """从环境变量读取指定云厂商的配置"""
    prefix = provider.upper()
    ak = os.getenv(f"{prefix}_ACCESS_KEY")
    sk = os.getenv(f"{prefix}_SECRET_KEY")
    region = os.getenv(f"{prefix}_REGION") or os.getenv(f"{prefix}_ENDPOINT")
    if not all([ak, sk, region]):
        raise EnvironmentError(f"未找到 {provider} 配置信息")
    return OssConfig(ak, sk, region)
