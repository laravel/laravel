import os
import sys
import requests

JIRA_URL = os.getenv("JIRA_URL")
JIRA_USER = os.getenv("JIRA_USER")
JIRA_TOKEN = os.getenv("JIRA_TOKEN")

def post_to_jira(issue_key, pr_url):
    api_url = f"{JIRA_URL}/rest/api/3/issue/{issue_key}/comment"
    auth = (JIRA_USER, JIRA_TOKEN)
    headers = {"Content-Type": "application/json"}
    payload = {"body": f"🤖 AI-generated fix PR created: {pr_url}"}
    res = requests.post(api_url, auth=auth, headers=headers, json=payload)
    if res.status_code not in [200, 201]:
        print(f"❌ Failed to post to Jira: {res.status_code}, {res.text}")
    else:
        print("✅ Comment posted to Jira")

if __name__ == "__main__":
    issue_key = sys.argv[1]
    with open("output/pr_url.txt") as f:
        pr_url = f.read().strip()
    post_to_jira(issue_key, pr_url)
