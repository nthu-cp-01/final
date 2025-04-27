# 如何將infra布署至aws上

## 0. 安裝Terraform:
- https://developer.hashicorp.com/terraform/tutorials/aws-get-started/install-cli#install-terraform

## 1. 打開Learner Lab，並按`Start Lab`(需確定裡面是乾淨的，如果有東西記得先`Reset`掉):
![image](https://github.com/user-attachments/assets/74b62e14-9544-43bd-9a6c-7aca19ca80c3)

## 2. 設定AWS CLI的Credential:
- 點`AWS Detials`，並複制CLI的Credential:
![image](https://github.com/user-attachments/assets/0d1f9af5-f9a9-47c5-b4c8-0cfa396ad944)
![image](https://github.com/user-attachments/assets/48364ab1-9378-417c-b260-3586b1bf5ae7)

- 到`~/.aws/credentials`，打開檔案並貼入credential:
<img width="1728" alt="image" src="https://github.com/user-attachments/assets/e1fbc331-a591-4ea2-844e-7e1b0ad34f1b" />
<img width="1728" alt="image" src="https://github.com/user-attachments/assets/bb288f8e-3611-4829-867e-cfb5a54aff74" />

- 存檔離開。

## 3. 切到`deployment/infra`目錄下，並執行terraform指令:
```bash
terraform init
terraform apply
```
<img width="1728" alt="image" src="https://github.com/user-attachments/assets/87bebefb-271f-4cd9-9ecb-2da0d3d4bd3c" />
<img width="1728" alt="image" src="https://github.com/user-attachments/assets/ecc2fe0e-7263-4b1a-b73c-3c072c79f32a" />

- 如果有問你問題，就回yes，然後Enter。
![image](https://github.com/user-attachments/assets/a1556ffe-0128-40bc-bd84-d5f1227b2322)

- 等它跑完，就會出現下面的畫面，同時會印出我們App的Application Load Balancer的DNS，只要複制後就可以貼進browser打開:
![image](https://github.com/user-attachments/assets/6f77c8df-2f7a-431b-9ed6-7ebc0e53cfe0)

## 4. (TBD) 執行Database Migration...
