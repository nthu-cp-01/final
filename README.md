# API使用指南

## 0. 登入你要掃描的使用者身份，並去Settings->API Tokens去產生你驗證用的token
- 如果要測試掃描流程，可以先用`bob@test.com`, `aoeuaoeu`這個帳號登入，然後產生API Token。
![image](https://github.com/user-attachments/assets/1da25db9-eaac-4d4f-aefd-8929d70db910)
![image](https://github.com/user-attachments/assets/149f779b-0b76-4ab8-8cd7-a2c822236883)
![image](https://github.com/user-attachments/assets/591bf9ff-a0da-4cfc-aead-e5120e3ffcf4)


## 1. 驗證你拿到的token是否是正確的(掃使用者的QR Code)
```
curl -k -X GET -H "Authorization: Bearer <使用者token>" https://<你的load-balancer dns位置>/api/user
```
- 正確的話會得到200
- 錯誤的話會得到302

## 2. 發送掃描的資料到後端Server
- 實際操作時`{"id":"<item 的 id>"}`的部分會包在QR Code中。(預計)
- 正確的回應都會是200，json裡會有回應正確的原因。
- 如果錯誤，除了json格式錯誤、item id(都會回422)不存在之外，應該都會回401。
```
curl -k -X POST -H "Authorization: Bearer <使用者token>" -H "Content-Type: application/json" --data '{"id":"<item 的 id>"}' https://<你的load-balancer dns位置>/api/scan
```
- 預設的系統內應該已經建好一個已核凖的出借表單，然後目標出借的項目id是2。(LG Monitor什麼的)
- 如果想即時觀測出借流程的結果，除了API回覆的訊息之外，也可以參考:
  - https://<你的load-balancer dns位置>/loaning-forms/2
- 如果是預先建好的這筆測資(LG Monitor)的出借，那指令就會變成:
```
curl -k -X POST -H "Authorization: Bearer <使用者token>" -H "Content-Type: application/json" --data '{"id":"2"}' https://<你的load-balancer dns位置>/api/scan
```
