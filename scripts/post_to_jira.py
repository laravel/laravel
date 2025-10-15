import os
import sys
import requests
import json

USE_DUMMY = "true" == "true"

JIRA_URL = os.getenv("JIRA_URL")
JIRA_USER = os.getenv("JIRA_USER")
JIRA_TOKEN = os.getenv("JIRA_TOKEN")

def post_to_jira(issue_key, pr_url):
    # -------------------------------
    # ADF COMMENT PAYLOAD
    # -------------------------------
    payload = {
        "body": {
            "type": "doc",
            "version": 1,
            "content": [
                {
                    "type": "paragraph",
                    "content": [
                        {"type": "text", "text": "✅ Test comment via Python script."}
                    ],
                },
                {
                    "type": "paragraph",
                    "content": [
                        {"type": "text", "text": "This is using the ADF structure and should appear correctly in Jira."}
                    ],
                },
                {
                    "type": "paragraph",
                    "content": [
                        {
                            "type": "text",
                            "text": "Check out dummy PR link",
                        },
                        {
                            "type": "text",
                            "text": " here.",
                            "marks": [
                                {"type": "link", "attrs": {"href": "https://dummy-pr-link.example.com"}}
                            ],
                        },
                    ],
                },
            ],
        }
    }

    # -------------------------------
    # HTTP REQUEST
    # -------------------------------
    api_url = f"{JIRA_URL}/rest/api/3/issue/{issue_key}/comment"
    headers = {"Content-Type": "application/json"}

    print(f"Posting to: {api_url}")
    response = requests.post(api_url, auth=(JIRA_USER, JIRA_TOKEN), headers=headers, json=payload)
    # -------------------------------
    # RESPONSE HANDLING
    # -------------------------------
    if response.status_code in [200, 201]:
        print("✅ Successfully posted to Jira!")
        print(json.dumps(response.json(), indent=2))
        return True
    else:
        print(f"❌ Jira responded with {response.status_code}")
        print(response.text)
        return False
    


if __name__ == "__main__":
    issue_key = sys.argv[1]

    # Fallback dummy PR link if none exists
    pr_url = "https://dummy-pr-link.example.com"
    if os.path.exists("output/pr_url.txt"):
        with open("output/pr_url.txt") as f:
            pr_url = f.read().strip()

    post_to_jira(issue_key, pr_url)
