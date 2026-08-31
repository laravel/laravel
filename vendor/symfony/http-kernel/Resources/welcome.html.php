<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$renderSymfonyLogoSvg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" height="48" width="48" viewBox="0 0 64.9 64.9"><path fill="currentColor" d="M32.5 0A32.5 32.5 0 0 0 0 32.5a32.5 32.5 0 0 0 32.5 32.4 32.5 32.5 0 0 0 32.4-32.4A32.5 32.5 0 0 0 32.5 0Zm14.1 12c3.3-.1 5.8 1.4 6 3.8 0 1-.6 3-2.6 3-1.5 0-2.6-.9-2.6-2.2 0-.5 0-1 .4-1.5.4-.6.4-.7.4-1 0-.9-1.3-.9-1.7-.9-4.8.2-6.1 6.8-7.2 12.1l-.5 2.8c2.8.4 4.8 0 6-.8 1.5-1-.5-2-.2-3.2a2.3 2.3 0 0 1 2.1-1.8c1.2 0 2 1.2 2 2.5 0 2-2.7 5-8.2 4.8l-2-.1-1 5.7c-.9 4.3-2.1 10.3-6.5 15.4a13.4 13.4 0 0 1-9.4 5.3c-3.2.1-5.4-1.6-5.4-3.9-.1-2.2 1.9-3.4 3.1-3.5 1.8 0 3 1.2 3 2.6 0 1.3-.6 1.6-1 1.9-.3.2-.7.4-.7 1 0 .1.2.6 1 .6 1.3 0 2.2-.7 2.9-1.2 3.1-2.6 4.3-7.1 5.9-15.4l.3-2c.6-2.8 1.2-5.8 2-8.8-2.1-1.6-3.5-3.7-6.4-4.5-2-.6-3.3-.1-4.2 1a3 3 0 0 0 .3 4l1.7 1.9c2 2.3 3.1 4.1 2.7 6.6-.7 3.9-5.3 6.9-10.9 5.2-4.7-1.4-5.6-4.8-5-6.6.5-1.6 1.8-2 3-1.6 1.4.5 2 2 1.5 3.3 0 .2 0 .4-.2.7l-.6 1c-.3 1 1 1.7 2 2 2.1.7 4.2-.4 4.7-2.1.5-1.6-.5-2.7-1-3.1l-2-2.1c-.8-1-2.8-3.9-1.9-7a6.8 6.8 0 0 1 2.4-3.5c2.4-1.8 5-2 7.6-1.3 3.2.9 4.8 3 6.8 4.7a28 28 0 0 1 5.1-9.3c2.2-2.6 5-4.4 8.3-4.5z"/></svg>
SVG;

// SVG icons from the Tabler Icons project
// MIT License - Copyright (c) 2020-2023 Paweł Kuna
// https://github.com/tabler/tabler-icons/blob/master/LICENSE

$renderBoxIconSvg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" class="icon icon-tabler icon-tabler-box" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
    <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
    <path d="M12 12l8 -4.5" />
    <path d="M12 12l0 9" />
    <path d="M12 12l-8 -4.5" />
</svg>
SVG;

$renderFolderIconSvg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" class="icon icon-tabler icon-tabler-folder-open" width="40" height="40" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
    <path d="M5 19l2.757 -7.351a1 1 0 0 1 .936 -.649h12.307a1 1 0 0 1 .986 1.164l-.996 5.211a2 2 0 0 1 -1.964 1.625h-14.026a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2h4l3 3h7a2 2 0 0 1 2 2v2"></path>
</svg>
SVG;

$renderInfoIconSvg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" class="icon icon-tabler icon-tabler-info-circle" width="40" height="40" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
    <path d="M12 9h.01"></path>
    <path d="M11 12h1v4h1"></path>
</svg>
SVG;

$renderNextStepIconSvg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" class="icon icon-tabler icon-tabler-square-chevrons-right" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
    <path d="M8 9l3 3l-3 3" />
    <path d="M13 9l3 3l-3 3" />
    <path d="M3 5a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-14z" />
</svg>
SVG;

$renderLearnIconSvg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" class="icon icon-tabler icon-tabler-book" width="40" height="40" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
    <path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"></path>
    <path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"></path>
    <path d="M3 6l0 13"></path>
    <path d="M12 6l0 13"></path>
    <path d="M21 6l0 13"></path>
</svg>
SVG;

$renderCommunityIconSvg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" class="icon icon-tabler icon-tabler-users" width="40" height="40" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
    <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
</svg>
SVG;

$renderUpdatesIconSvg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" class="icon icon-tabler icon-tabler-bell-ringing" width="40" height="40" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
    <path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
    <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
    <path d="M21 6.727a11.05 11.05 0 0 0 -2.794 -3.727" />
    <path d="M3 6.727a11.05 11.05 0 0 1 2.792 -3.727" />
</svg>
SVG;

$renderWavesSvg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" style="pointer-events: none" class="wave" width="100%" height="50px" preserveAspectRatio="none" viewBox="0 0 1920 75">
    <defs>
        <style>
            .a { fill: none; }
            .b { clip-path: url(#a); }
            .c, .d { fill: var(--light-color); }
            .d { opacity: 0.5; isolation: isolate; }
            @keyframes waveMotion {
                0% { transform: scaleX(1) translateX(0); }
                50% { transform: scaleX(1.5) translateX(-30%); }
                0% { transform: scaleX(1) translateX(0); }
            }

            .b:nth-child(5) { animation: waveMotion 14s infinite alternate; }
            .b:nth-child(2) { animation: waveMotion 12s infinite alternate; }
            .b:nth-child(3) { animation: waveMotion 13s infinite alternate; }
            .b:nth-child(4) { animation: waveMotion 8s infinite alternate; }

            @media (prefers-reduced-motion) {
                .b { animation: none !important; }
            }
        </style>
        <clipPath id="a"><rect class="a" width="1920" height="75"></rect></clipPath>
    </defs>
    <g class="b">
        <path class="c" d="M1963,327H-105V65A2647.49,2647.49,0,0,1,431,19c217.7,3.5,239.6,30.8,470,36,297.3,6.7,367.5-36.2,642-28a2511.41,2511.41,0,0,1,420,48"></path>
    </g>
    <g class="b">
        <path class="d" d="M-127,404H1963V44c-140.1-28-343.3-46.7-566,22-75.5,23.3-118.5,45.9-162,64-48.6,20.2-404.7,128-784,0C355.2,97.7,341.6,78.3,235,50,86.6,10.6-41.8,6.9-127,10"></path>
    </g>
    <g class="b">
        <path class="d" d="M1979,462-155,446V106C251.8,20.2,576.6,15.9,805,30c167.4,10.3,322.3,32.9,680,56,207,13.4,378,20.3,494,24"></path>
    </g>
    <g class="b">
        <path class="d" d="M1998,484H-243V100c445.8,26.8,794.2-4.1,1035-39,141-20.4,231.1-40.1,378-45,349.6-11.6,636.7,73.8,828,150"></path>
    </g>
</svg>
SVG;
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noodp, notranslate, noimageindex" />
    <title>Welcome to Symfony!</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 128 128%22><text y=%221.2em%22 font-size=%2296%22>👋</text></svg>" />
    <style>
        :root {
            --hue: <?php echo random_int(0, 360); ?>;
            --light-color: hsl(var(--hue), 20%, 95%);
            --dark-color: hsl(var(--hue), 20%, 40%);
        }

        body {
            -webkit-text-size-adjust: 100%;
            background: var(--light-color);
            color: var(--dark-color);
            font-feature-settings: normal;
            font-family: ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;
            margin: 0;
            padding: 0;
        }

        @keyframes stars{
            0% { transform: translateX(0) translateY(0px); }
            33% { transform: translateX(-20px)  translateY(-20px); }
            66% { transform: translateX(-40px) translateY(-40px); }
            100% { transform: translateX(0px)  translateY(0px); }
        }
        header a, main a { color: inherit; text-decoration: underline; text-decoration-thickness: 1px; text-underline-offset: 3px; }
        header a:hover, main a:hover { text-decoration-thickness: 3px; text-decoration-skip-ink: none; }
        header a:active, main a:active { position: relative; top: 1px; }
        header { background: radial-gradient(ellipse at bottom, var(--dark-color) 0%, hsl(var(--hue), 20%, 13%) 100%); background-attachment: fixed; color: var(--light-color); overflow: hidden; position: relative }
        header:before { content: ''; position: absolute; background: transparent; width: 100%; height: 100%; backdrop-filter: blur(1px); z-index: 1; }
        @media (prefers-reduced-motion) {
            header:after { animation: none !important; }
        }
        header:after { animation: stars 30s infinite; content: ""; position: absolute; height: 2px; width: 2px; top: -2px; left: 0; background: white; box-shadow: 778px 1019px 0 0 rgba(255, 255, 255, 0.826) , 1075px 1688px 0 0 rgba(255,255,255, 0.275) , 388px 1021px 0 0 rgba(255,255,255, 0.259) , 1238px 626px 0 0 rgba(255,255,255, 0.469) , 997px 904px 0 0 rgba(255,255,255, 0.925) , 921px 1345px 0 0 rgba(255,255,255, 0.698) , 337px 1236px 0 0 rgba(255,255,255, 0.838) , 460px 569px 0 0 rgba(255,255,255, 0.01) , 690px 1488px 0 0 rgba(255,255,255, 0.154) , 859px 926px 0 0 rgba(255,255,255, 0.515) , 1272px 791px 0 0 rgba(255,255,255, 1) , 238px 1256px 0 0 rgba(255,255,255, 0.633) , 1486px 897px 0 0 rgba(255,255,255, 0.88) , 667px 6px 0 0 rgba(255,255,255, 0.508) , 853px 504px 0 0 rgba(255,255,255, 0.248) , 1329px 1778px 0 0 rgba(255,255,255, 0.217) , 768px 1340px 0 0 rgba(255,255,255, 0.792) , 631px 1383px 0 0 rgba(255,255,255, 0.698) , 991px 1603px 0 0 rgba(255,255,255, 0.939) , 1778px 1767px 0 0 rgba(255,255,255, 0.784) , 285px 546px 0 0 rgba(255,255,255, 0.8) , 1224px 1333px 0 0 rgba(255,255,255, 0.676) , 1154px 397px 0 0 rgba(255,255,255, 0.974) , 1210px 1004px 0 0 rgba(255,255,255, 0.894) , 1632px 953px 0 0 rgba(255,255,255, 0.281) , 449px 1144px 0 0 rgba(255,255,255, 0.706) , 1426px 771px 0 0 rgba(255,255,255, 0.737) , 1438px 1634px 0 0 rgba(255,255,255, 0.984) , 806px 168px 0 0 rgba(255,255,255, 0.807) , 731px 1067px 0 0 rgba(255,255,255, 0.734) , 1731px 1785px 0 0 rgba(255,255,255, 0.528) , 23px 975px 0 0 rgba(255,255,255, 0.068) , 575px 1088px 0 0 rgba(255,255,255, 0.876) , 1205px 1668px 0 0 rgba(255,255,255, 0.601) , 18px 1457px 0 0 rgba(255,255,255, 0.176) , 252px 1163px 0 0 rgba(255,255,255, 0.416) , 1752px 1px 0 0 rgba(255,255,255, 0.374) , 382px 767px 0 0 rgba(255,255,255, 0.073) , 133px 1462px 0 0 rgba(255,255,255, 0.706) , 851px 1166px 0 0 rgba(255,255,255, 0.535) , 374px 921px 0 0 rgba(255,255,255, 0.548) , 554px 1598px 0 0 rgba(255,255,255, 0.062) , 314px 685px 0 0 rgba(255,255,255, 0.187) , 1443px 209px 0 0 rgba(255,255,255, 0.097) , 1774px 1625px 0 0 rgba(255,255,255, 0.32) , 58px 278px 0 0 rgba(255,255,255, 0.684) , 986px 338px 0 0 rgba(255,255,255, 0.272) , 718px 1357px 0 0 rgba(255,255,255, 0.317) , 722px 983px 0 0 rgba(255,255,255, 0.568) , 1124px 992px 0 0 rgba(255,255,255, 0.199) , 581px 619px 0 0 rgba(255,255,255, 0.44) , 1120px 285px 0 0 rgba(255,255,255, 0.425) , 702px 138px 0 0 rgba(255,255,255, 0.816) , 262px 767px 0 0 rgba(255,255,255, 0.92) , 1204px 38px 0 0 rgba(255,255,255, 0.197) , 1196px 410px 0 0 rgba(255,255,255, 0.453) , 707px 699px 0 0 rgba(255,255,255, 0.481) , 1590px 1488px 0 0 rgba(255,255,255, 0.559) , 879px 1763px 0 0 rgba(255,255,255, 0.241) , 106px 686px 0 0 rgba(255,255,255, 0.175) , 158px 569px 0 0 rgba(255,255,255, 0.549) , 711px 1219px 0 0 rgba(255,255,255, 0.476) , 1339px 53px 0 0 rgba(255,255,255, 0.275) , 1410px 172px 0 0 rgba(255,255,255, 0.449) , 1601px 1484px 0 0 rgba(255,255,255, 0.988) , 1328px 1752px 0 0 rgba(255,255,255, 0.827) , 1733px 1475px 0 0 rgba(255,255,255, 0.567) , 559px 742px 0 0 rgba(255,255,255, 0.423) , 772px 844px 0 0 rgba(255,255,255, 0.039) , 602px 520px 0 0 rgba(255,255,255, 0.284) , 1158px 1067px 0 0 rgba(255,255,255, 0.066) , 1562px 730px 0 0 rgba(255,255,255, 0.086) , 1792px 615px 0 0 rgba(255,255,255, 0.438) , 1085px 1191px 0 0 rgba(255,255,255, 0.157) , 1402px 1087px 0 0 rgba(255,255,255, 0.797) , 569px 1685px 0 0 rgba(255,255,255, 0.992) , 1608px 52px 0 0 rgba(255,255,255, 0.302) , 1697px 1246px 0 0 rgba(255,255,255, 0.295) , 899px 1490px 0 0 rgba(255,255,255, 0.73) , 993px 901px 0 0 rgba(255,255,255, 0.961) , 1193px 1023px 0 0 rgba(255,255,255, 0.671) , 1224px 176px 0 0 rgba(255,255,255, 0.786) , 721px 1308px 0 0 rgba(255,255,255, 0.691) , 1702px 730px 0 0 rgba(255,255,255, 0.841) , 1480px 1498px 0 0 rgba(255,255,255, 0.655) , 181px 1612px 0 0 rgba(255,255,255, 0.588) , 1776px 679px 0 0 rgba(255,255,255, 0.821) , 892px 706px 0 0 rgba(255,255,255, 0.056) , 859px 267px 0 0 rgba(255,255,255, 0.565) , 784px 1285px 0 0 rgba(255,255,255, 0.029) , 1561px 1198px 0 0 rgba(255,255,255, 0.315) , 205px 421px 0 0 rgba(255,255,255, 0.584) , 236px 406px 0 0 rgba(255,255,255, 0.166) , 1259px 689px 0 0 rgba(255,255,255, 0.321) , 448px 317px 0 0 rgba(255,255,255, 0.495) , 1318px 466px 0 0 rgba(255,255,255, 0.275) , 1053px 297px 0 0 rgba(255,255,255, 0.035) , 716px 538px 0 0 rgba(255,255,255, 0.764) , 381px 207px 0 0 rgba(255,255,255, 0.692) , 871px 1140px 0 0 rgba(255,255,255, 0.342) , 361px 53px 0 0 rgba(255,255,255, 0.984) , 1565px 1593px 0 0 rgba(255,255,255, 0.102) , 145px 277px 0 0 rgba(255,255,255, 0.866) , 220px 1503px 0 0 rgba(255,255,255, 0.936) , 1068px 1475px 0 0 rgba(255,255,255, 0.156) , 1548px 483px 0 0 rgba(255,255,255, 0.768) , 710px 103px 0 0 rgba(255,255,255, 0.809) , 1660px 921px 0 0 rgba(255,255,255, 0.952) , 462px 1252px 0 0 rgba(255,255,255, 0.825) , 1123px 1628px 0 0 rgba(255,255,255, 0.409) , 1274px 729px 0 0 rgba(255,255,255, 0.26) , 1739px 679px 0 0 rgba(255,255,255, 0.83) , 1550px 1518px 0 0 rgba(255,255,255, 0.25) , 1624px 346px 0 0 rgba(255,255,255, 0.557) , 1023px 579px 0 0 rgba(255,255,255, 0.854) , 217px 661px 0 0 rgba(255,255,255, 0.731) , 1504px 549px 0 0 rgba(255,255,255, 0.705) , 939px 5px 0 0 rgba(255,255,255, 0.389) , 284px 735px 0 0 rgba(255,255,255, 0.355) , 13px 1679px 0 0 rgba(255,255,255, 0.712) , 137px 1592px 0 0 rgba(255,255,255, 0.619) , 1113px 505px 0 0 rgba(255,255,255, 0.651) , 1584px 510px 0 0 rgba(255,255,255, 0.41) , 346px 913px 0 0 rgba(255,255,255, 0.09) , 198px 1490px 0 0 rgba(255,255,255, 0.103) , 447px 1128px 0 0 rgba(255,255,255, 0.314) , 1356px 324px 0 0 rgba(255,255,255, 0.324) , 648px 667px 0 0 rgba(255,255,255, 0.155) , 442px 260px 0 0 rgba(255,255,255, 0.22) , 210px 401px 0 0 rgba(255,255,255, 0.682) , 422px 1772px 0 0 rgba(255,255,255, 0.671) , 276px 349px 0 0 rgba(255,255,255, 0.683) , 131px 539px 0 0 rgba(255,255,255, 0.977) , 892px 94px 0 0 rgba(255,255,255, 0.081) , 1295px 222px 0 0 rgba(255,255,255, 0.961) , 5px 1727px 0 0 rgba(255,255,255, 0.311) , 714px 1148px 0 0 rgba(255,255,255, 0.846) , 1455px 1182px 0 0 rgba(255,255,255, 0.313) , 1370px 708px 0 0 rgba(255,255,255, 0.824) , 812px 433px 0 0 rgba(255,255,255, 0.75) , 1110px 558px 0 0 rgba(255,255,255, 0.709) , 1132px 1543px 0 0 rgba(255,255,255, 0.868) , 644px 610px 0 0 rgba(255,255,255, 0.166) , 269px 1481px 0 0 rgba(255,255,255, 0.889) , 1712px 590px 0 0 rgba(255,255,255, 0.139) , 1159px 599px 0 0 rgba(255,255,255, 0.992) , 1551px 209px 0 0 rgba(255,255,255, 0.033) , 1020px 1721px 0 0 rgba(255,255,255, 0.028) , 216px 373px 0 0 rgba(255,255,255, 0.665) , 877px 532px 0 0 rgba(255,255,255, 0.686) , 1326px 885px 0 0 rgba(255,255,255, 0.517) , 972px 1704px 0 0 rgba(255,255,255, 0.499) , 749px 181px 0 0 rgba(255,255,255, 0.712) , 1511px 1650px 0 0 rgba(255,255,255, 0.101) , 1432px 183px 0 0 rgba(255,255,255, 0.545) , 1541px 1338px 0 0 rgba(255,255,255, 0.71) , 513px 1406px 0 0 rgba(255,255,255, 0.17) , 1314px 1197px 0 0 rgba(255,255,255, 0.789) , 824px 1659px 0 0 rgba(255,255,255, 0.597) , 308px 298px 0 0 rgba(255,255,255, 0.917) , 1225px 659px 0 0 rgba(255,255,255, 0.229) , 1253px 257px 0 0 rgba(255,255,255, 0.631) , 1653px 185px 0 0 rgba(255,255,255, 0.113) , 336px 614px 0 0 rgba(255,255,255, 0.045) , 1093px 898px 0 0 rgba(255,255,255, 0.617) , 730px 5px 0 0 rgba(255,255,255, 0.11) , 785px 645px 0 0 rgba(255,255,255, 0.516) , 989px 678px 0 0 rgba(255,255,255, 0.917) , 1511px 1614px 0 0 rgba(255,255,255, 0.938) , 584px 1117px 0 0 rgba(255,255,255, 0.631) , 534px 1012px 0 0 rgba(255,255,255, 0.668) , 1325px 1778px 0 0 rgba(255,255,255, 0.293) , 1632px 754px 0 0 rgba(255,255,255, 0.26) , 78px 1258px 0 0 rgba(255,255,255, 0.52) , 779px 1691px 0 0 rgba(255,255,255, 0.878) , 253px 1706px 0 0 rgba(255,255,255, 0.75) , 1358px 245px 0 0 rgba(255,255,255, 0.027) , 361px 1629px 0 0 rgba(255,255,255, 0.238) , 1134px 232px 0 0 rgba(255,255,255, 0.387) , 1685px 777px 0 0 rgba(255,255,255, 0.156) , 515px 724px 0 0 rgba(255,255,255, 0.863) , 588px 1728px 0 0 rgba(255,255,255, 0.159) , 1132px 47px 0 0 rgba(255,255,255, 0.691) , 315px 1446px 0 0 rgba(255,255,255, 0.782) , 79px 233px 0 0 rgba(255,255,255, 0.317) , 1498px 1050px 0 0 rgba(255,255,255, 0.358) , 30px 1073px 0 0 rgba(255,255,255, 0.939) , 1637px 620px 0 0 rgba(255,255,255, 0.141) , 1736px 1683px 0 0 rgba(255,255,255, 0.682) , 1298px 1505px 0 0 rgba(255,255,255, 0.863) , 972px 85px 0 0 rgba(255,255,255, 0.941) , 349px 1356px 0 0 rgba(255,255,255, 0.672) , 1545px 1429px 0 0 rgba(255,255,255, 0.859) , 1076px 467px 0 0 rgba(255,255,255, 0.024) , 189px 1647px 0 0 rgba(255,255,255, 0.838) , 423px 1722px 0 0 rgba(255,255,255, 0.771) , 1691px 1719px 0 0 rgba(255,255,255, 0.676) , 1747px 658px 0 0 rgba(255,255,255, 0.255) , 149px 1492px 0 0 rgba(255,255,255, 0.911) , 1203px 1138px 0 0 rgba(255,255,255, 0.964) , 781px 1584px 0 0 rgba(255,255,255, 0.465) , 1609px 1595px 0 0 rgba(255,255,255, 0.688) , 447px 1655px 0 0 rgba(255,255,255, 0.166) , 914px 1153px 0 0 rgba(255,255,255, 0.085) , 600px 1058px 0 0 rgba(255,255,255, 0.821) , 804px 505px 0 0 rgba(255,255,255, 0.608) , 1506px 584px 0 0 rgba(255,255,255, 0.618) , 587px 1290px 0 0 rgba(255,255,255, 0.071) , 258px 600px 0 0 rgba(255,255,255, 0.243) , 328px 395px 0 0 rgba(255,255,255, 0.065) , 846px 783px 0 0 rgba(255,255,255, 0.995) , 1138px 1294px 0 0 rgba(255,255,255, 0.703) , 1668px 633px 0 0 rgba(255,255,255, 0.27) , 337px 103px 0 0 rgba(255,255,255, 0.202) , 132px 986px 0 0 rgba(255,255,255, 0.726) , 414px 757px 0 0 rgba(255,255,255, 0.752) , 8px 1311px 0 0 rgba(255,255,255, 0.307) , 1791px 910px 0 0 rgba(255,255,255, 0.346) , 844px 216px 0 0 rgba(255,255,255, 0.156) , 1547px 1723px 0 0 rgba(255,255,255, 0.73) , 1187px 398px 0 0 rgba(255,255,255, 0.698) , 1550px 1520px 0 0 rgba(255,255,255, 0.462) , 1346px 655px 0 0 rgba(255,255,255, 0.58) , 668px 770px 0 0 rgba(255,255,255, 0.422) , 1774px 1435px 0 0 rgba(255,255,255, 0.089) , 693px 1061px 0 0 rgba(255,255,255, 0.893) , 132px 1689px 0 0 rgba(255,255,255, 0.937) , 894px 1561px 0 0 rgba(255,255,255, 0.88) , 906px 1706px 0 0 rgba(255,255,255, 0.567) , 1140px 297px 0 0 rgba(255,255,255, 0.358) , 13px 1288px 0 0 rgba(255,255,255, 0.464) , 1744px 423px 0 0 rgba(255,255,255, 0.845) , 119px 1548px 0 0 rgba(255,255,255, 0.769) , 1249px 1321px 0 0 rgba(255,255,255, 0.29) , 123px 795px 0 0 rgba(255,255,255, 0.597) , 390px 1542px 0 0 rgba(255,255,255, 0.47) , 825px 667px 0 0 rgba(255,255,255, 0.049) , 1071px 875px 0 0 rgba(255,255,255, 0.06) , 1428px 1786px 0 0 rgba(255,255,255, 0.222) , 993px 696px 0 0 rgba(255,255,255, 0.399) , 1585px 247px 0 0 rgba(255,255,255, 0.094) , 1340px 1312px 0 0 rgba(255,255,255, 0.603) , 1640px 725px 0 0 rgba(255,255,255, 0.026) , 1161px 1397px 0 0 rgba(255,255,255, 0.222) , 966px 1132px 0 0 rgba(255,255,255, 0.69) , 1782px 1275px 0 0 rgba(255,255,255, 0.606) , 1117px 1533px 0 0 rgba(255,255,255, 0.248) , 1027px 959px 0 0 rgba(255,255,255, 0.46) , 459px 839px 0 0 rgba(255,255,255, 0.98) , 1192px 265px 0 0 rgba(255,255,255, 0.523) , 175px 501px 0 0 rgba(255,255,255, 0.371) , 626px 19px 0 0 rgba(255,255,255, 0.246) , 46px 1173px 0 0 rgba(255,255,255, 0.124) , 573px 925px 0 0 rgba(255,255,255, 0.621) , 1px 283px 0 0 rgba(255,255,255, 0.943) , 778px 1213px 0 0 rgba(255,255,255, 0.128) , 435px 593px 0 0 rgba(255,255,255, 0.378) , 32px 394px 0 0 rgba(255,255,255, 0.451) , 1019px 1055px 0 0 rgba(255,255,255, 0.685) , 1423px 1233px 0 0 rgba(255,255,255, 0.354) , 494px 841px 0 0 rgba(255,255,255, 0.322) , 667px 194px 0 0 rgba(255,255,255, 0.655) , 1671px 195px 0 0 rgba(255,255,255, 0.502) , 403px 1710px 0 0 rgba(255,255,255, 0.623) , 665px 1597px 0 0 rgba(255,255,255, 0.839) , 61px 1742px 0 0 rgba(255,255,255, 0.566) , 1490px 1654px 0 0 rgba(255,255,255, 0.646) , 1361px 1604px 0 0 rgba(255,255,255, 0.101) , 1191px 1023px 0 0 rgba(255,255,255, 0.881) , 550px 378px 0 0 rgba(255,255,255, 0.573) , 1332px 1234px 0 0 rgba(255,255,255, 0.922) , 760px 1205px 0 0 rgba(255,255,255, 0.992) , 1506px 1328px 0 0 rgba(255,255,255, 0.723) , 1126px 813px 0 0 rgba(255,255,255, 0.549) , 67px 240px 0 0 rgba(255,255,255, 0.901) , 125px 1301px 0 0 rgba(255,255,255, 0.464) , 643px 391px 0 0 rgba(255,255,255, 0.589) , 1114px 1756px 0 0 rgba(255,255,255, 0.321) , 1602px 699px 0 0 rgba(255,255,255, 0.274) , 510px 393px 0 0 rgba(255,255,255, 0.185) , 171px 1217px 0 0 rgba(255,255,255, 0.932) , 1202px 1362px 0 0 rgba(255,255,255, 0.726) , 1160px 1324px 0 0 rgba(255,255,255, 0.867) , 121px 319px 0 0 rgba(255,255,255, 0.992) , 1474px 835px 0 0 rgba(255,255,255, 0.89) , 357px 1213px 0 0 rgba(255,255,255, 0.91) , 783px 976px 0 0 rgba(255,255,255, 0.941) , 750px 1599px 0 0 rgba(255,255,255, 0.515) , 323px 450px 0 0 rgba(255,255,255, 0.966) , 1078px 282px 0 0 rgba(255,255,255, 0.947) , 1164px 46px 0 0 rgba(255,255,255, 0.296) , 1792px 705px 0 0 rgba(255,255,255, 0.485) , 880px 1287px 0 0 rgba(255,255,255, 0.894) , 60px 1402px 0 0 rgba(255,255,255, 0.816) , 752px 894px 0 0 rgba(255,255,255, 0.803) , 285px 1535px 0 0 rgba(255,255,255, 0.93) , 1528px 401px 0 0 rgba(255,255,255, 0.727) , 651px 1767px 0 0 rgba(255,255,255, 0.146) , 1498px 1190px 0 0 rgba(255,255,255, 0.042) , 394px 1786px 0 0 rgba(255,255,255, 0.159) , 1318px 9px 0 0 rgba(255,255,255, 0.575) , 1699px 1675px 0 0 rgba(255,255,255, 0.511) , 82px 986px 0 0 rgba(255,255,255, 0.906) , 940px 970px 0 0 rgba(255,255,255, 0.562) , 1624px 259px 0 0 rgba(255,255,255, 0.537) , 1782px 222px 0 0 rgba(255,255,255, 0.259) , 1572px 1725px 0 0 rgba(255,255,255, 0.716) , 1080px 1557px 0 0 rgba(255,255,255, 0.245) , 1727px 648px 0 0 rgba(255,255,255, 0.471) , 899px 231px 0 0 rgba(255,255,255, 0.445) , 1061px 1074px 0 0 rgba(255,255,255, 0.079) , 556px 478px 0 0 rgba(255,255,255, 0.524) , 343px 359px 0 0 rgba(255,255,255, 0.162) , 711px 1254px 0 0 rgba(255,255,255, 0.323) , 1335px 242px 0 0 rgba(255,255,255, 0.936) , 933px 39px 0 0 rgba(255,255,255, 0.784) , 1629px 908px 0 0 rgba(255,255,255, 0.289) , 1800px 229px 0 0 rgba(255,255,255, 0.399) , 1589px 926px 0 0 rgba(255,255,255, 0.709) , 976px 694px 0 0 rgba(255,255,255, 0.855) , 1163px 1240px 0 0 rgba(255,255,255, 0.754) , 1662px 1784px 0 0 rgba(255,255,255, 0.088) , 656px 1388px 0 0 rgba(255,255,255, 0.688) , 1190px 1100px 0 0 rgba(255,255,255, 0.769) , 33px 392px 0 0 rgba(255,255,255, 0.301) , 56px 1405px 0 0 rgba(255,255,255, 0.969) , 1491px 118px 0 0 rgba(255,255,255, 0.991) , 1216px 997px 0 0 rgba(255,255,255, 0.727) , 1617px 712px 0 0 rgba(255,255,255, 0.45) , 163px 553px 0 0 rgba(255,255,255, 0.977) , 103px 140px 0 0 rgba(255,255,255, 0.916) , 1099px 1404px 0 0 rgba(255,255,255, 0.167) , 1423px 587px 0 0 rgba(255,255,255, 0.792) , 1797px 309px 0 0 rgba(255,255,255, 0.526) , 381px 141px 0 0 rgba(255,255,255, 0.005) , 1214px 802px 0 0 rgba(255,255,255, 0.887) , 211px 829px 0 0 rgba(255,255,255, 0.72) , 1103px 1507px 0 0 rgba(255,255,255, 0.642) , 244px 1231px 0 0 rgba(255,255,255, 0.184) , 118px 1747px 0 0 rgba(255,255,255, 0.475) , 183px 1293px 0 0 rgba(255,255,255, 0.148) , 911px 1362px 0 0 rgba(255,255,255, 0.073) , 817px 457px 0 0 rgba(255,255,255, 0.459) , 756px 18px 0 0 rgba(255,255,255, 0.544) , 481px 1118px 0 0 rgba(255,255,255, 0.878) , 380px 138px 0 0 rgba(255,255,255, 0.132) , 320px 646px 0 0 rgba(255,255,255, 0.04) , 1724px 1716px 0 0 rgba(255,255,255, 0.381) , 978px 1269px 0 0 rgba(255,255,255, 0.431) , 1530px 255px 0 0 rgba(255,255,255, 0.31) , 664px 204px 0 0 rgba(255,255,255, 0.913) , 474px 703px 0 0 rgba(255,255,255, 0.832) , 1722px 1204px 0 0 rgba(255,255,255, 0.356) , 1453px 821px 0 0 rgba(255,255,255, 0.195) , 730px 1468px 0 0 rgba(255,255,255, 0.696) , 928px 1610px 0 0 rgba(255,255,255, 0.894) , 1036px 304px 0 0 rgba(255,255,255, 0.696) , 1590px 172px 0 0 rgba(255,255,255, 0.729) , 249px 1590px 0 0 rgba(255,255,255, 0.277) , 357px 81px 0 0 rgba(255,255,255, 0.526) , 726px 1261px 0 0 rgba(255,255,255, 0.149) , 643px 946px 0 0 rgba(255,255,255, 0.005) , 1263px 995px 0 0 rgba(255,255,255, 0.124) , 1564px 1107px 0 0 rgba(255,255,255, 0.789) , 388px 83px 0 0 rgba(255,255,255, 0.498) , 715px 681px 0 0 rgba(255,255,255, 0.655) , 1618px 1624px 0 0 rgba(255,255,255, 0.63) , 1423px 1576px 0 0 rgba(255,255,255, 0.52) , 564px 1786px 0 0 rgba(255,255,255, 0.482) , 1066px 735px 0 0 rgba(255,255,255, 0.276) , 714px 1179px 0 0 rgba(255,255,255, 0.395) , 967px 1006px 0 0 rgba(255,255,255, 0.923) , 1136px 1790px 0 0 rgba(255,255,255, 0.801) , 215px 1690px 0 0 rgba(255,255,255, 0.957) , 1500px 1338px 0 0 rgba(255,255,255, 0.541) , 1679px 1065px 0 0 rgba(255,255,255, 0.925) , 426px 1489px 0 0 rgba(255,255,255, 0.193) , 1273px 853px 0 0 rgba(255,255,255, 0.317) , 665px 1189px 0 0 rgba(255,255,255, 0.512) , 520px 552px 0 0 rgba(255,255,255, 0.925) , 253px 438px 0 0 rgba(255,255,255, 0.588) , 369px 1354px 0 0 rgba(255,255,255, 0.889) , 749px 205px 0 0 rgba(255,255,255, 0.243) , 820px 145px 0 0 rgba(255,255,255, 0.207) , 1739px 228px 0 0 rgba(255,255,255, 0.267) , 392px 495px 0 0 rgba(255,255,255, 0.504) , 721px 1044px 0 0 rgba(255,255,255, 0.823) , 833px 912px 0 0 rgba(255,255,255, 0.222) , 865px 1499px 0 0 rgba(255,255,255, 0.003) , 313px 756px 0 0 rgba(255,255,255, 0.727) , 439px 1187px 0 0 rgba(255,255,255, 0.572) , 6px 1238px 0 0 rgba(255,255,255, 0.676) , 1567px 11px 0 0 rgba(255,255,255, 0.701) , 1216px 757px 0 0 rgba(255,255,255, 0.87) , 916px 588px 0 0 rgba(255,255,255, 0.565) , 831px 215px 0 0 rgba(255,255,255, 0.597) , 1289px 697px 0 0 rgba(255,255,255, 0.964) , 307px 34px 0 0 rgba(255,255,255, 0.462) , 3px 1685px 0 0 rgba(255,255,255, 0.464) , 1115px 1421px 0 0 rgba(255,255,255, 0.303) , 1451px 473px 0 0 rgba(255,255,255, 0.142) , 1374px 1205px 0 0 rgba(255,255,255, 0.086) , 1564px 317px 0 0 rgba(255,255,255, 0.773) , 304px 1127px 0 0 rgba(255,255,255, 0.653) , 446px 214px 0 0 rgba(255,255,255, 0.135) , 1541px 459px 0 0 rgba(255,255,255, 0.725) , 1387px 880px 0 0 rgba(255,255,255, 0.157) , 1172px 224px 0 0 rgba(255,255,255, 0.088) , 1420px 637px 0 0 rgba(255,255,255, 0.916) , 1385px 932px 0 0 rgba(255,255,255, 0.225) , 174px 1472px 0 0 rgba(255,255,255, 0.649) , 252px 750px 0 0 rgba(255,255,255, 0.277) , 825px 1042px 0 0 rgba(255,255,255, 0.707) , 840px 703px 0 0 rgba(255,255,255, 0.948) , 1478px 1800px 0 0 rgba(255,255,255, 0.151) , 95px 1303px 0 0 rgba(255,255,255, 0.332) , 1198px 740px 0 0 rgba(255,255,255, 0.443) , 141px 312px 0 0 rgba(255,255,255, 0.04) , 291px 729px 0 0 rgba(255,255,255, 0.284) , 1209px 1506px 0 0 rgba(255,255,255, 0.741) , 1188px 307px 0 0 rgba(255,255,255, 0.141) , 958px 41px 0 0 rgba(255,255,255, 0.858) , 1311px 1484px 0 0 rgba(255,255,255, 0.097) , 846px 1153px 0 0 rgba(255,255,255, 0.862) , 1238px 1376px 0 0 rgba(255,255,255, 0.071) , 1499px 342px 0 0 rgba(255,255,255, 0.719) , 640px 833px 0 0 rgba(255,255,255, 0.966) , 712px 545px 0 0 rgba(255,255,255, 0.194) , 1655px 1542px 0 0 rgba(255,255,255, 0.82) , 616px 353px 0 0 rgba(255,255,255, 0.871) , 1591px 1631px 0 0 rgba(255,255,255, 0.61) , 1664px 591px 0 0 rgba(255,255,255, 0.35) , 934px 454px 0 0 rgba(255,255,255, 0.58) , 1175px 477px 0 0 rgba(255,255,255, 0.966) , 299px 914px 0 0 rgba(255,255,255, 0.839) , 534px 243px 0 0 rgba(255,255,255, 0.194) , 773px 1135px 0 0 rgba(255,255,255, 0.42) , 1696px 1472px 0 0 rgba(255,255,255, 0.552) , 125px 523px 0 0 rgba(255,255,255, 0.591) , 1195px 382px 0 0 rgba(255,255,255, 0.904) , 1609px 1374px 0 0 rgba(255,255,255, 0.579) , 843px 82px 0 0 rgba(255,255,255, 0.072) , 1604px 451px 0 0 rgba(255,255,255, 0.545) , 1322px 190px 0 0 rgba(255,255,255, 0.034) , 528px 228px 0 0 rgba(255,255,255, 0.146) , 1470px 1169px 0 0 rgba(255,255,255, 0.912) , 502px 1350px 0 0 rgba(255,255,255, 0.594) , 1031px 298px 0 0 rgba(255,255,255, 0.368) , 1100px 1427px 0 0 rgba(255,255,255, 0.79) , 979px 1105px 0 0 rgba(255,255,255, 0.973) , 643px 1184px 0 0 rgba(255,255,255, 0.813) , 1636px 1701px 0 0 rgba(255,255,255, 0.013) , 1004px 245px 0 0 rgba(255,255,255, 0.412) , 680px 740px 0 0 rgba(255,255,255, 0.967) , 1599px 562px 0 0 rgba(255,255,255, 0.66) , 256px 1617px 0 0 rgba(255,255,255, 0.463) , 314px 1092px 0 0 rgba(255,255,255, 0.734) , 870px 900px 0 0 rgba(255,255,255, 0.512) , 530px 60px 0 0 rgba(255,255,255, 0.198) , 1786px 896px 0 0 rgba(255,255,255, 0.392) , 636px 212px 0 0 rgba(255,255,255, 0.997) , 672px 540px 0 0 rgba(255,255,255, 0.632) , 1118px 1649px 0 0 rgba(255,255,255, 0.377) , 433px 647px 0 0 rgba(255,255,255, 0.902) , 1200px 1737px 0 0 rgba(255,255,255, 0.262) , 1258px 143px 0 0 rgba(255,255,255, 0.729) , 1603px 1364px 0 0 rgba(255,255,255, 0.192) , 66px 1756px 0 0 rgba(255,255,255, 0.681) , 946px 263px 0 0 rgba(255,255,255, 0.105) , 1216px 1082px 0 0 rgba(255,255,255, 0.287) , 6px 1143px 0 0 rgba(255,255,255, 0.017) , 1631px 126px 0 0 rgba(255,255,255, 0.449) , 357px 1565px 0 0 rgba(255,255,255, 0.163) , 1752px 261px 0 0 rgba(255,255,255, 0.423) , 1247px 1631px 0 0 rgba(255,255,255, 0.312) , 320px 671px 0 0 rgba(255,255,255, 0.695) , 1375px 596px 0 0 rgba(255,255,255, 0.856) , 1456px 1340px 0 0 rgba(255,255,255, 0.564) , 447px 1044px 0 0 rgba(255,255,255, 0.623) , 1732px 447px 0 0 rgba(255,255,255, 0.216) , 174px 1509px 0 0 rgba(255,255,255, 0.398) , 16px 861px 0 0 rgba(255,255,255, 0.904) , 878px 1296px 0 0 rgba(255,255,255, 0.205) , 1725px 1483px 0 0 rgba(255,255,255, 0.704) , 255px 48px 0 0 rgba(255,255,255, 0.7) , 610px 1669px 0 0 rgba(255,255,255, 0.865) , 1044px 1251px 0 0 rgba(255,255,255, 0.98) , 884px 862px 0 0 rgba(255,255,255, 0.198) , 986px 545px 0 0 rgba(255,255,255, 0.379) , 1620px 217px 0 0 rgba(255,255,255, 0.159) , 383px 1763px 0 0 rgba(255,255,255, 0.518) , 595px 974px 0 0 rgba(255,255,255, 0.347) , 359px 14px 0 0 rgba(255,255,255, 0.863) , 95px 1385px 0 0 rgba(255,255,255, 0.011) , 411px 1030px 0 0 rgba(255,255,255, 0.038) , 345px 789px 0 0 rgba(255,255,255, 0.771) , 421px 460px 0 0 rgba(255,255,255, 0.133) , 972px 1160px 0 0 rgba(255,255,255, 0.342) , 597px 1061px 0 0 rgba(255,255,255, 0.781) , 1017px 1092px 0 0 rgba(255,255,255, 0.437); }
        .wrapper { margin: 0 auto; max-width: 64rem; padding: 0 20px; position: relative; }
        .logo { display: flex; margin-top: 45px; }
        .logo svg { height: 48px; width: 48px; margin-right: 15px; }
        .logo h1 { font-size: 32px; line-height: 1; margin: 0; }
        .logo h1 small { display: block; font-size: 18px; }
        .info { padding-bottom: 60px; }
        .info ul { list-style: none; margin: 30px 0; padding: 0; }
        .info li { display: flex; }
        .info li + li { margin-top: 30px; }
        .info svg { height: 20px; width: 20px; margin-right: 10px; flex-shrink: 0; }
        .info code { display: block; font-family: ui-monospace, SFMono-Regular, SF Mono, Menlo, Consolas, Liberation Mono, monospace; font-size: .9em; margin-top: 5px; }
        .info .next-step { background: rgba(255, 255, 255, 0.2); border-radius: 4px; line-height: 1.5; margin: 0 0 20px; padding: 10px 15px; }
        .info .next-step strong { display: block; text-transform: uppercase; font-size: 90%; }
        .info .next-step svg { display: none; }
        .waves { color: #3a3a52; position: absolute; bottom: -5px; left: 0; right: 0; width: 100% }
        main article { padding: 30px 0 0; }
        main h2 { align-items: center; display: flex; font-size: 18px; font-weight: 600; margin: 0; }
        main h2 span { display: inline-flex; }
        main h2 svg { height: 28px; width: 28px; margin-right: 10px; }
        main article section + section { margin-top: 45px; }
        main article section ul { padding-left: 23px; }
        main article section li + li { margin-top: 15px; }
        header .wrapper, header .waves, .sf-toolbar {  z-index: 10; }

        @media (min-width: 768px) {
            @keyframes fade-in { 0% { opacity: 0; } 100% { opacity: 1; } }
            .sf-toolbar { opacity: 0; animation: fade-in 1s .2s forwards; position: relative; z-index: 99999; }
            @media (prefers-reduced-motion) {
                .sf-toolbar { animation: none !important; opacity: 1; }
            }

            body { font-size: 18px; }
            .wrapper { padding: 0 30px; }
            .logo { margin-top: 75px; }
            .logo svg { height: 72px; width: 72px; margin-right: 30px; }
            .logo h1 { font-size: 48px; }
            .logo h1 small { font-size: 24px; }
            .info ul { margin: 45px 0; padding-left: 15px; }
            .info li + li { margin-top: 35px; }
            .info svg { height: 28px; width: 28px; margin-right: 13px; position: relative; top: -2px; }
            .info code { margin-top: 10px; }
            .info .next-step { display: flex; align-items: center; margin: 0 0 45px; padding: 15px; }
            .info .next-step svg { display: inline-flex; margin: 0 10px; top: 0; }
            main article nav { display: flex; justify-content: space-between; padding-top: 45px; }
            main article section + section { margin-top: 0; }
            main h2 { font-size: 21px; }
            main h2 svg { height: 36px; width: 36px; margin-right: 15px; }
            main article section ul { padding-left: 28px; }
            main article section li + li { margin-top: 20px; }
        }

        @media (min-width: 1280px) {
            body { font-size: 20px; }
            .logo { margin-top: 105px; }
            .logo svg { height: 96px; width: 96px; margin-right: 45px; }
            .logo h1 { font-size: 64px; }
            .logo h1 small { font-size: 36px; }
            .info ul { margin: 60px 0; padding-left: 30px; }
            .info li + li { margin-top: 45px; }
            .info svg { height: 32px; width: 32px; margin-right: 15px; position: relative; top: -4px; }
            .info .next-step { padding: 15px 30px; margin: 0 0 60px; }
            .info .next-step svg { margin: 0 15px; }
            main article { padding-top: 60px; }
            main h2 { align-items: flex-start; flex-direction: column; font-size: 24px; }
            main h2 svg { display: block; height: 48px; width: 48px; margin-bottom: 10px; margin-left: -4px; }
            main article section ul { padding-left: 20px; }
            main article section li + li { margin-top: 25px; }
        }
    </style>
</head>
<body>
    <header>
        <div class="wrapper">
            <section class="logo">
                <?php echo $renderSymfonyLogoSvg; ?>
                <h1>
                    <small>Welcome to</small>
                    <span translate="no" class="notranslate">Symfony</span> <?php echo explode('.', $version, 2)[0]; ?>
                </h1>
            </section>

            <section class="info">
                <ul>
                    <li>
                        <?php echo $renderBoxIconSvg; ?>
                        <span>You are using Symfony <strong><?php echo $version; ?></strong> version</span>
                    </li>

                    <li>
                        <?php echo $renderFolderIconSvg; ?>
                        <span>Your application is ready at: <code translate="no" class="notranslate project_dir_path"><?php echo $projectDir; ?></code></span>
                    </li>

                    <li>
                        <?php echo $renderInfoIconSvg; ?>
                        <span>You are seeing this page because the homepage URL is not configured and <a target="_blank" href="https://symfony.com/doc/<?php echo $docVersion; ?>/debug-mode">debug mode</a> is enabled.</span>
                    </li>
                </ul>

                <p class="next-step" role="note">
                    <strong>Next Step</strong>
                    <?php echo $renderNextStepIconSvg; ?>
                    <span>
                        <a target="_blank" href="https://symfony.com/doc/<?php echo $docVersion; ?>/page_creation.html">Create your first page</a>
                        to replace this placeholder page.
                    </span>
                </p>
            </section>
        </div>

        <section class="waves" aria-hidden="true" role="separator">
            <?php echo $renderWavesSvg; ?>
        </section>
    </header>

    <main>
        <div class="wrapper">
            <article>
                <nav>
                    <section>
                        <h2>
                            <span><?php echo $renderLearnIconSvg; ?></span>
                            Learn
                        </h2>
                        <ul>
                            <li>
                                <a target="_blank" href="https://symfony.com/doc/<?php echo $docVersion; ?>">Read Symfony Docs</a>
                            </li>
                            <li>
                                <a target="_blank" href="https://symfonycasts.com/screencast/symfony">Watch Symfony Screencast</a>
                            </li>
                            <li>
                                <a target="_blank" href="https://symfony.com/book">Read Symfony Book</a>
                            </li>
                        </ul>
                    </section>

                    <section>
                        <h2>
                            <span><?php echo $renderCommunityIconSvg; ?></span>
                            Community & Support
                        </h2>
                        <ul>
                            <li>
                                <a target="_blank" href="https://symfony.com/support">Symfony Support</a>
                            </li>
                            <li>
                                <a target="_blank" href="https://symfony.com/community">Join the Symfony Community</a>
                            </li>
                            <li>
                                <a target="_blank" href="https://symfony.com/doc/current/contributing/index.html">Contribute to Symfony</a>
                            </li>
                        </ul>
                    </section>

                    <section>
                        <h2>
                            <span><?php echo $renderUpdatesIconSvg; ?></span>
                            Stay Updated
                        </h2>
                        <ul>
                            <li>
                                <a target="_blank" href="https://symfony.com/blog/">Symfony Blog</a>
                            </li>
                            <li>
                                <a target="_blank" href="https://symfony.com/community#interact">Follow Symfony</a>
                            </li>
                            <li>
                                <a target="_blank" href="https://symfony.com/events/">Attend Symfony Events</a>
                            </li>
                        </ul>
                    </section>
                </nav>
            </article>
        </div>
    </main>
</body>
</html>
