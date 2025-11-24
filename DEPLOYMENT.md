# CI/CD Setup Guide

## GitHub Secrets Configuration

You need to add these secrets to your GitHub repository for automated deployment:

### Navigate to Settings
1. Go to your repository on GitHub
2. Click **Settings** → **Secrets and variables** → **Actions**
3. Click **New repository secret**

### Required Secrets

Add each of these secrets:

#### 1. `BACKEND_SERVER_IP`
- The public IP of your backend server
- Get it with: `terraform output backend_public_ip`

#### 2. `SSH_PRIVATE_KEY`
- Your SSH private key to access the server
- Copy your private key: `cat ~/.ssh/id_rsa` (or your key path)
- Paste the entire key including `-----BEGIN` and `-----END` lines

#### 3. `DB_HOST`
- Get it with: `terraform output database_host`

#### 4. `DB_PORT`
- Get it with: `terraform output database_port`

#### 5. `DB_DATABASE`
- Get it with: `terraform output database_name`

#### 6. `DB_USERNAME`
- Get it with: `terraform output database_user`

#### 7. `DB_PASSWORD`
- Get it with: `terraform output -raw database_password`

#### 8. `APP_KEY` (Optional)
- Leave empty initially, it will be auto-generated on first deploy
- Or generate one: `php artisan key:generate --show`

#### 9. `APP_URL`
- Your backend server URL: `http://YOUR_BACKEND_IP`

## Quick Setup Script

Run this from your `my-terraform` directory to get all values:

```bash
echo "=== GitHub Secrets Values ==="
echo ""
echo "BACKEND_SERVER_IP:"
terraform output -raw backend_public_ip
echo ""
echo ""
echo "DB_HOST:"
terraform output -raw database_host
echo ""
echo ""
echo "DB_PORT:"
terraform output -raw database_port
echo ""
echo ""
echo "DB_DATABASE:"
terraform output -raw database_name
echo ""
echo ""
echo "DB_USERNAME:"
terraform output -raw database_user
echo ""
echo ""
echo "DB_PASSWORD:"
terraform output -raw database_password
echo ""
echo ""
echo "APP_URL:"
echo "http://$(terraform output -raw backend_public_ip)"
echo ""
```

## How It Works

1. **Push to Repository**: Every push to `main` or `master` triggers deployment
2. **GitHub Actions**: Connects to your backend server via SSH
3. **Deployment**: Pulls code, installs dependencies, updates .env, runs migrations
4. **Restart**: Restarts PHP-FPM and Nginx

## Manual Trigger

You can also manually trigger deployment:
1. Go to **Actions** tab in GitHub
2. Select **Deploy to Backend Server**
3. Click **Run workflow**

## First Deployment

After setting up secrets:
```bash
cd laravel-Obelion-task
git add .
git commit -m "Initial deployment setup"
git push origin main
```

Then watch the deployment in the **Actions** tab!
