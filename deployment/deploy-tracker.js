const axios = require('axios')

const [slackToken, CHANNEL, env, from, to, githubToken] = process.argv.slice(2)
const slack_token = `Bearer ${slackToken}`;
const github_token = `token ${githubToken}`;


if(from && to) {
  axios
    .get(
      `https://api.github.com/repos/demiansc/smart-trust-api/compare/${from}...${to}`,
       {
         headers: {
           Authorization: github_token,
         }
       }
    )
    .then(res => {
      const { commits } = res.data
      const enters = new RegExp('\n+', 'g')

      const commitMessages = commits.map(commit => ({
        fallback: commit.commit.message.replace(enters, ' - '),
        color: '#2eb886',
        title: commit.commit.message.split('\n')[0],
        title_link: commit.html_url,
        text: commit.commit.message
          .split('\n')
          .slice(1)
          .join('\n'),
        footer: commit.commit.author.name,
        footer_icon: commit.author.avatar_url,
      }))

      axios
        .post(
          'https://slack.com/api/chat.postMessage',
          {
            icon_url: 'https://smart-trust.digbang.com/static/logo.svg',
            username: 'Deploy tracker',
            channel: CHANNEL,
            text: `Deploy started for *${env}*${
              commitMessages.length ? ` with ${commitMessages.length} new commits:` : ' with no new commits.'
            }`,
            pretty: 1,
            attachments: commitMessages.slice(0, 100),
          },
          {
            headers: {
              Authorization: slack_token,
            },
          }
        )
        .then(function(response) {
          //console.log(response)
        })
        .catch(function(error) {
          console.log(error)
        })
    })
    .catch(function(error) {
      console.log(error)
    })
}


if(! from && !to) {
  axios
    .post(
      'https://slack.com/api/chat.postMessage',
      {
        icon_url: 'https://smart-trust.digbang.com/static/logo.svg',
        username: 'Deploy tracker',
        channel: CHANNEL,
        text: `Deploy finished successfully for *${env}*`,
        pretty: 1,
      },
      {
        headers: {
          Authorization:
          slack_token,
        },
      }
    )
    .then(function(response) {
      //console.log(response)
    })
    .catch(function(error) {
      console.log(error)
    })

}
