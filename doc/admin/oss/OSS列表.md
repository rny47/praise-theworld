
访问地址： `/admin/storage/oss/list`
请求参数类型： `form-data` 
请求方式：`get`
接口输入：

#### 接口请求数据：

| 参数名称 | 类型 | 是否必选 | 备注     |
| :------- | :--- | :------: | :------- |
| `page`   | int  |    Y     | 显示几页 |
| `limit`  | int  |    Y     | 显示条数 |
 


#### 接口响应数据：

| 名称      | 类型   | 必选 | 说明        |
| --------- | ------ | ---- | ----------- |
| `code`    | string | 是   | 状态码      |
| `message` | string | 是   | 消息提示    |
| `data`    | json   | 是   | 详看 `data` |



`data数据：`

| 参数名称     | 参数类型 | 是否必选 | 说明                  |
| ------------ | -------- | -------- | --------------------- |
| `page`       | int      | Y        | 页码                  |
| `total_page` | int      | Y        | 分页数量              |
| `total`      | int      | Y        | 显示总条数            |
| `limit`      | string   | Y        | 显示条数              |
| `item`       | json     | Y        | 业务数据，详看 `item` |


`item数据：`

| 参数名称       | 参数类型  | 是否必选 | 说明                                                                  |
| -------------- | --------- | -------- | --------------------------------------------------------------------- |
| `o_id`         | int       | 是       | id                                                                    |
| `o_name`       | string    | 是       | OSS名字                                                               |
| `o_status`     | int       | 是       | 状态：0=正常,1=删除,2=过度                                            |
| `o_quicken`    | int       | 是       | 开启加速：0=不开启,1=开启                                             |
| `o_type`       | string    | 是       | 类型：tencent=腾讯,s3=亚马逊,minio=MinIO                              |
| `o_host`       | string    | 否       | 存储云地址，只有在类型为MinIO的时候，该参数必填，其他类型掩藏无需传递 |
| `o_key_id`     | string    | 是       | KeyId                                                                 |
| `o_key_secret` | string    | 是       | KeySecret                                                             |
| `o_app_id`     | string    | 是       | 桶应用ID                                                              |
| `o_region`     | string    | 是       | 区域                                                                  |
| `created_at`   | timestamp | 否       | 添加时间                                                              |
| `updated_at`   | timestamp | 否       | 删除时间                                                              |
| `deleted_at`   | timestamp | 否       |                                                                       |





`错误编码数据：`

| 参数名称 | 说明         |
| -------- | ------------ |
| 000      | 成功         |
| 991      | 请先登录！   |
| 997      | 参数错误！   |
| 998      | 路由未定义！ |
| 999      | 系统异常！   |

