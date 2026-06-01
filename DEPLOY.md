# Deploy (GitHub Actions → FTP)

This project includes a GitHub Actions workflow that deploys the repository to your cPanel `public_html` via FTP on every push to `main` or `master`.

Steps to enable continuous deploy:

1. Create a GitHub repository named `juaben2006connect`, then connect and push this project to it:
   ```bash
   git branch -M main
   git remote add origin https://github.com/<your-github-username>/juaben2006connect.git
   git push -u origin main
   ```

2. In your GitHub repo, go to `Settings` → `Secrets and variables` → `Actions` and add these secrets:
   - `FTP_SERVER` — your cPanel FTP host (e.g., ftp.yourdomain.com)
   - `FTP_USERNAME` — FTP username
   - `FTP_PASSWORD` — FTP password
   - `FTP_SERVER_DIR` — remote directory to deploy to (e.g., `/public_html` or `/public_html/alumni`)

3. Push to `main`. GitHub Actions will run the workflow and upload changed files to the FTP server.

Notes:
- The workflow excludes `.git`, `.github`, `node_modules`, `vendor`, `tests` and `*.zip` files.
- If you prefer SFTP, change the action or use an SFTP action and corresponding secrets.
- Make sure `APP_URL` and DB settings are updated for production (see `config/config.php` and `config/database.php`).
