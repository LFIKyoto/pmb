<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="xml" indent="yes"/>
	<xsl:param name="cp_artevod"/>
	<xsl:template match="/wsObjectListQuery">
		<unimarc>
			<xsl:for-each select="film">
				<notice>
					<bl>m</bl>
					<hl>0</hl>
					<dt>g</dt>
					<xsl:call-template name="lang"/>
					<xsl:call-template name="title"/>
					<xsl:call-template name="publisher"/>
					<xsl:call-template name="collation"/>
					<xsl:call-template name="notes"/>
					<xsl:call-template name="tags"/>
					<xsl:call-template name="authors"/>
					<xsl:call-template name="links"/>
					<xsl:call-template name="source"/>
				</notice>
			</xsl:for-each>
		</unimarc>
	</xsl:template>
	
	<xsl:template name="title">
		<f c='200'>
			<xsl:choose>
				<xsl:when test="editorial/title">
					<s c='a'><xsl:value-of select="editorial/title"/></s>
					<xsl:if test="editorial/original_title!=''">
						<s c='d'><xsl:value-of select="editorial/original_title"/></s>
					</xsl:if>
				</xsl:when>
				<xsl:when test="editorial/original_title">
					<s c='a'><xsl:value-of select="editorial/original_title"/></s>
				</xsl:when>
				<xsl:otherwise>
					<s c='a'>Sans titre</s>
				</xsl:otherwise>
			</xsl:choose>
		</f>
	</xsl:template>
	
	<xsl:template name="lang">
		<xsl:if test="technical/languages/language!=''">
			<f c='101'>
				<s c='a'><xsl:value-of select="document('corresp_lang.xml')//entry[@artecode=current()/technical/languages/language/@code]/@unicode"/></s>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="publisher">
		<xsl:variable name="zone_210">
			<xsl:if test="technical/production_countries/country/label!=''">
				<s c='a'><xsl:value-of select="technical/production_countries/country/label"/></s>
			</xsl:if>
			<xsl:if test="technical/release_dates!=''">
				<s c='d'><xsl:value-of select="technical/release_dates"/></s>
			</xsl:if>
		</xsl:variable>
		<xsl:if test="$zone_210">
			<f c='210'>
				<xsl:copy-of select="$zone_210"/>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="collation">
		<xsl:if test="technical/duration!=''">
			<f c='215'>
				<s c='a'><xsl:value-of select="concat(technical/duration,' min')"/></s>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="notes">
		<xsl:if test="technical/target_audience/label!=''">
			<f c='300'>
				<s c='a'><xsl:value-of select="technical/target_audience/label"/></s>
			</f>
		</xsl:if>
		<xsl:if test="editorial/body!=''">
			<f c='327'>
				<s c='a'><xsl:value-of select="editorial/body"/></s>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="tags">
		<xsl:if test="count(editorial/tags/tag)">
			<xsl:for-each select="editorial/tags/tag">
				<f c='610'>
					<s c='a'><xsl:value-of select="label"/></s>
				</f>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="authors">
		<xsl:for-each select="staff/authors/person">
			<f>
				<xsl:attribute name="c">
					<xsl:choose>
						<xsl:when test="position()=1">
							<xsl:text>700</xsl:text>
						</xsl:when>
						<xsl:otherwise><xsl:text>701</xsl:text></xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
				<s c='a'><xsl:value-of select="last_name"/></s>
				<s c='b'><xsl:value-of select="first_name"/></s>
			</f>
		</xsl:for-each>
		<xsl:for-each select="staff/actors/person">
			<f c='702'>
				<s c='a'><xsl:value-of select="last_name"/></s>
				<s c='b'><xsl:value-of select="first_name"/></s>
			</f>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="links">
		<f c='856'>
			<s c='u'><xsl:value-of select="externalUri"/></s>
		</f>
		<xsl:if test="media/posters/media/@src!=''">
			<f c='896'>
				<s c='a'><xsl:value-of select="media/posters/media/@src"/></s>
			</f>
		</xsl:if>
		<f c='900'>
			<s c='a'><xsl:value-of select="pk"/></s>
			<s c='n'><xsl:value-of select="$cp_artevod"/></s>
		</f>
	</xsl:template>
	
	<xsl:template name="source">
		<f c="801">
			<s c="a">FR</s>
			<s c="b">ArteVOD</s>
		</f>
	</xsl:template>
</xsl:stylesheet>