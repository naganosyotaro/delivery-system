# Renderへのデプロイ手順

## 前提条件
- GitHubアカウント
- Renderアカウント（無料登録: https://render.com）

---

## Step 1: GitHubリポジトリを作成

```bash
cd "/Users/siroemon/project/Simple_Delivery_Management _System"

# Gitリポジトリ初期化
git init
git add .
git commit -m "Initial commit: 配送管理システム"

# GitHubでリポジトリを作成後、以下を実行
git remote add origin https://github.com/YOUR_USERNAME/delivery-system.git
git branch -M main
git push -u origin main
```

---

## Step 2: Renderでデプロイ

1. [Render Dashboard](https://dashboard.render.com) にログイン

2. **New +** → **Web Service** をクリック

3. **Build and deploy from a Git repository** を選択

4. GitHubリポジトリを連携して選択

5. 以下の設定を入力：
   - **Name**: `delivery-system`（任意）
   - **Region**: `Singapore (Southeast Asia)` ← 日本に近い
   - **Branch**: `main`
   - **Runtime**: `Docker`
   - **Instance Type**: `Free`

6. **Environment Variables** に以下を追加：
   | Key | Value |
   |-----|-------|
   | APP_NAME | 配送管理システム |
   | APP_ENV | production |
   | APP_DEBUG | false |
   | APP_KEY | `php artisan key:generate --show` の出力値 |
   | LOG_CHANNEL | stderr |
   | DB_CONNECTION | sqlite |
   | SESSION_DRIVER | cookie |

7. **Create Web Service** をクリック

---

## Step 3: 確認

デプロイが完了すると、以下のようなURLが発行されます：
```
https://delivery-system-xxxx.onrender.com
```

このURLをお客様に共有してください！

---

## ⚠️ 注意事項

### 無料プランの制限
- **15分間アクセスがないとスリープ**します
- 次回アクセス時に30秒ほど起動に時間がかかります
- お客様へのデモ前に一度アクセスしておくことをお勧めします

### データについて
- 無料プランではSQLiteを使用するため、再デプロイ時にデータがリセットされます
- 本番運用には有料プラン + PostgreSQLをお勧めします

---

## APP_KEYの生成

ローカルで以下を実行してコピー：
```bash
/usr/local/opt/php/bin/php artisan key:generate --show
```
出力例: `base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=`
