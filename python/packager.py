import subprocess
from pathlib import Path

def create_secure_apk(input_apk: str, output_dir: str) -> str:
    """调用外部打包工具生成防报毒 APK, 返回生成的文件路径"""
    output_dir = Path(output_dir)
    output_dir.mkdir(parents=True, exist_ok=True)
    output_apk = output_dir / (Path(input_apk).stem + "_secure.apk")
    cmd = ["/usr/bin/python3", "external_packager.py", input_apk, str(output_apk)]
    try:
        subprocess.run(cmd, check=True)
    except subprocess.CalledProcessError as e:
        raise RuntimeError(f"打包失败: {e}")
    return str(output_apk)
