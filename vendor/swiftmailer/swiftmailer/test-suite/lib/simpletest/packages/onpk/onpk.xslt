<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:output method="html" indent="yes" />
	<xsl:preserve-space elements="*"/>
	
	<xsl:template match="/">
		<html>
			<xsl:call-template name="head"/>
			<xsl:call-template name="body"/>
		</html>
	</xsl:template>
	
	<xsl:template name="head">
		<head>
			<xsl:call-template name="meta"/>
			<link rel="stylesheet" type="text/css" href="../../onpk_article.css" title="Style pour les articles :: onpk ::" />
			<link rel="shortcut icon" href="../images/php.ico" />
			<title><xsl:value-of select="//long_title"/></title>
		</head>
	</xsl:template>
		
	<xsl:template name="meta">
		<meta http-equiv="content-language" content="fr" />
		<meta name="keywords">
			<xsl:attribute name="content">
				<xsl:value-of select="normalize-space(/page/meta/keywords)"/>
			</xsl:attribute>
		</meta>
		<meta name="description">
			<xsl:attribute name="content">
				<xsl:value-of select="normalize-space(/page/meta/description)"/>
			</xsl:attribute>
		</meta>
		<meta name="author" content="Marcus Baker, traduction Perrick Penet" />
		<meta name="DC.Title" content="Le filtrage d'URL pour le navigateur Opera" />
		<meta name="DC.Creator" content="Marcus Baker, traduction Perrick Penet" />
		<meta name="DC.Subject" content="Comment mettre en place le filtrage d'URL pour Opera" />
		<meta name="DC.Description">
			<xsl:attribute name="content">
				<xsl:value-of select="normalize-space(/page/meta/description)"/>
			</xsl:attribute>
		</meta>
		<meta name="DC.Date" content="2002-07-01" />
		<meta name="DC.Type" content="Text" />
		<meta name="DC.Format" content="text/html" />
		<meta name="DC.Identifier" content="http://www.onpk.net/opera/url_filtering/index.html" />
		<meta name="DC.Language" content="fr" />
	</xsl:template>
	
	<xsl:template name="body">
		<body>
			<xsl:call-template name="masthead"/>
			<div class="content">
				<xsl:apply-templates select="//content/node()"/>
			</div>
			<xsl:call-template name="list_files"/>
			<xsl:call-template name="further_reading"/>
			<xsl:call-template name="copyright"/>
		</body>
	</xsl:template>
			
	<xsl:template name="list_files">
		<xsl:variable name="map" select="document('map_onpk.xml')/page"/>
		<xsl:choose>
			<xsl:when test="/page/@here = 'php / simpletest'">
				<div class="menu">
					<xsl:call-template name="list_layer">
						<xsl:with-param name="map" select="$map"/>
					</xsl:call-template>
				</div>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template name="list_item">
		<xsl:param name="map"/>
		<a>
			<xsl:attribute name="href"><xsl:value-of select="$map/@file"/></xsl:attribute>
			<xsl:value-of select="$map/@title"/>
		</a>
	</xsl:template>
	
	<xsl:template name="list_layer">
		<xsl:param name="map"/>
		<xsl:if test="$map/page">
			<ul>
				<xsl:for-each select="$map/page">
					<li>
						<xsl:call-template name="show_list_entry">
							<xsl:with-param name="map" select="."/>
						</xsl:call-template>
					</li>
				</xsl:for-each>
			</ul>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="show_list_entry">
		<xsl:param name="map"/>
		<xsl:call-template name="list_item">
			<xsl:with-param name="map" select="$map"/>
		</xsl:call-template>
		<xsl:call-template name="list_layer">
			<xsl:with-param name="map" select="$map"/>
		</xsl:call-template>
	</xsl:template>
	
	<xsl:template name="masthead">
		<div class="card">
			<div class="menu">
				Tous les autres articles de <a href="index.php">:: onpk :: sur php/simpletest</a>.
			</div>
			<h1><xsl:value-of select="//page/@title"/></h1>
			<div class="in">
				<xsl:call-template name="internal_links"/>
			</div>
		</div>
	</xsl:template>
	
	<xsl:template name="internal_links">
		Cette page...
		<ul>
			<xsl:apply-templates select="//internal/link" mode="links"/>
		</ul>
	</xsl:template>
   
	<xsl:template name="external_links">
		Pour aller plus loin...
		<ul>
			<xsl:apply-templates select="//external/link" mode="links"/>
		</ul>
	</xsl:template>
   
	<xsl:template name="further_reading">
		<div class="out">
			<xsl:call-template name="external_links"/>
		</div>
	</xsl:template>

	<xsl:template name="copyright">
		<xsl:variable name="raw_title" select="//long_title"/>
		<xsl:choose>
			<xsl:when test="contains($raw_title, 'onpk')">
				<div class="copyright">
					Copyright <a href="mailto:perrick@onpk.net">Perrick Penet</a> 2004
				</div>
			</xsl:when>
			<xsl:otherwise>
				<div class="copyright">
					Copyright <a href="mailto:marcus@lastcraft.com">Marcus Baker</a> 2003<br />Traduction <a href="mailto:perrick@onpk.net">Perrick Penet</a> 2004
				</div>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="php">
		<pre class="code">
			<xsl:call-template name="preserve_strong">
				<xsl:with-param name="raw" select="."/>
			</xsl:call-template>
		</pre>
	</xsl:template>
	
	<xsl:template match="code">
		<span class="new_code">
			<xsl:apply-templates/>
		</span>
	</xsl:template>
	
	<xsl:template match="section">
		<p>
			<a class="target">
				<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
				<h2><xsl:value-of select="@title"/></h2>
			</a>
		</p>
		<xsl:apply-templates/>
	</xsl:template>
	
	<xsl:template match="introduction">
		<xsl:apply-templates/>
	</xsl:template>
	
	<xsl:template match="a">
		<xsl:copy>
			<xsl:for-each select="@class|@name|@href">
				<xsl:attribute name="{local-name(.)}"><xsl:value-of select="."/></xsl:attribute>
			</xsl:for-each>
			<xsl:for-each select="@local">
				<xsl:attribute name="href">
					<xsl:value-of select="."/><xsl:text>.php</xsl:text>
				</xsl:attribute>
			</xsl:for-each>
			<xsl:apply-templates/>
		</xsl:copy>
	</xsl:template>
	
	<xsl:template match="*">
		<xsl:copy>
			<xsl:for-each select="@*">
				<xsl:attribute name="{local-name(.)}"><xsl:value-of select="."/></xsl:attribute>
			</xsl:for-each>
			<xsl:apply-templates/>
		</xsl:copy>
	</xsl:template>
	
	<xsl:template match="*" mode="links">
		<li><xsl:apply-templates/></li>
	</xsl:template>
	
	<xsl:template name="preserve_strong">
		<xsl:param name="raw"/>
		<xsl:choose>
			<xsl:when test="contains($raw, '&lt;strong&gt;') and contains($raw, '&lt;/strong&gt;')">
				<xsl:value-of select="substring-before($raw, '&lt;strong&gt;')"/>
				<strong>
					<xsl:value-of select="substring-before(substring-after($raw, '&lt;strong&gt;'), '&lt;/strong&gt;')"/>
				</strong>
				<xsl:call-template name="preserve_strong">
					<xsl:with-param name="raw" select="substring-after($raw, '&lt;/strong&gt;')"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise><xsl:value-of select="$raw"/></xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>