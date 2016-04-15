<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	version="1.0">
	
	<xsl:output method="xml" indent="yes"/>
	
	<xsl:template match="/wsObjectListQuery">
		<unimarc>
			<xsl:for-each select="film">
				<notice>
					<xsl:element name="rs">*</xsl:element>
					<xsl:element name="ru">*</xsl:element>
					<xsl:element name="el">1</xsl:element>
					<xsl:element name="bl">m</xsl:element>
					<xsl:element name="hl">0</xsl:element>
					<xsl:element name="dt">g</xsl:element>
					<xsl:call-template name="cp_pk"/>
					<xsl:call-template name="eresource"/>
					<xsl:for-each select="editorial">
						<xsl:call-template name="title"/>
						<xsl:call-template name="resume"/>
						<xsl:call-template name="subject"/>
						<xsl:call-template name="cp_genre"/>
						<xsl:call-template name="cp_sub_genre"/>
					</xsl:for-each>
					<xsl:for-each select="technical">
						<xsl:call-template name="language"/>
						<xsl:call-template name="cp_duration"/>
						<xsl:call-template name="cp_target_audience"/>
						<xsl:call-template name="cp_production_year"/>
						<xsl:call-template name="cp_production_countries"/>
						<xsl:call-template name="cp_codes"/>
						<xsl:call-template name="release_dates"/>
						<xsl:call-template name="cp_copyright"/>
					</xsl:for-each>
					<xsl:for-each select="staff">
						<xsl:call-template name="responsabilities"/>
					</xsl:for-each>
					<xsl:for-each select="media">
						<xsl:call-template name="medias"/>
					</xsl:for-each>
					<xsl:for-each select="legal">
						<xsl:call-template name="cp_allowed_countries"/>
					</xsl:for-each>
					<xsl:call-template name="note_contenu"/>
					<xsl:call-template name="source"/>
				</notice>
			</xsl:for-each>
		</unimarc>
	</xsl:template>
	
	<xsl:template name="language">
		<xsl:for-each select="languages/language">
			<f c="101">
				<s c="c"><xsl:value-of select="@code"/></s>
			</f>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="title">
		<f c="200">
			<xsl:for-each select="title">
				<s c="a"><xsl:value-of select="."/></s>
			</xsl:for-each>
			<xsl:for-each select="editorial/original_title">
				<s c="d"><xsl:value-of select="."/></s>
			</xsl:for-each>
		</f>
	</xsl:template>
	
	<xsl:template name="note_contenu">
		<f c="327">
			<s c="a"><xsl:text>Vidéo à la demande</xsl:text></s>
		</f>
	</xsl:template>
	
	<xsl:template name="resume">
		<xsl:if test="description">
			<f c="330">
				<s c="a">
				<xsl:for-each select="description">
					<xsl:value-of select="."/>
				</xsl:for-each>
				<xsl:if test="body">
					<xsl:text> </xsl:text>
					<xsl:for-each select="body">
						<xsl:value-of select="."/>
					</xsl:for-each>				
				</xsl:if>
				</s>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="subject">
		<xsl:if test="tags/tag!=''">
			<xsl:for-each select="tags/tag/label">
				<f c="610">
					<s c="a"><xsl:value-of select="."></xsl:value-of></s>
				</f>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="responsabilities">
		<xsl:if test="authors/person">
			<xsl:for-each select="authors/person">
				<xsl:choose>
					<xsl:when test="position()=1">
						<f c="700">
							<s c="a"><xsl:value-of select="last_name"/></s>
							<s c="b"><xsl:value-of select="first_name"/></s>
							<s c="4">070</s>
						</f>
					</xsl:when>
					<xsl:otherwise>
						<f c="701">
							<s c="a"><xsl:value-of select="last_name"/></s>
							<s c="b"><xsl:value-of select="first_name"/></s>
							<s c="4">070</s>
						</f>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
		</xsl:if>
		<xsl:if test="actors/person">
			<xsl:for-each select="actors/person">
				<f c="702">
					<s c="a"><xsl:value-of select="last_name"/></s>
					<s c="b"><xsl:value-of select="first_name"/></s>
					<s c="4">005</s>
				</f>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="eresource">
		<xsl:if test="externalUri">
			<xsl:for-each select="externalUri">
				<f c="856">
					<s c="u"><xsl:value-of select="."/></s>
				</f>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="release_dates">
		<xsl:if test="release_dates/release_date">
			<f c="300">
				<s c="a">
					<xsl:for-each select="release_dates/release_date">
						<xsl:variable name="releaseDate"><xsl:value-of select="."/></xsl:variable>
						<xsl:choose>
							<xsl:when test="@code='CINE'">
								<xsl:text>Sortie au cinéma le : </xsl:text>
								<xsl:value-of select="concat(substring($releaseDate, 9, 2), '/', substring($releaseDate, 6, 2), '/', substring($releaseDate,1,4))"/>
								<xsl:text>.</xsl:text>
							</xsl:when>
							<xsl:when test="@code='VOD'">
								<xsl:text>Sortie en VOD le : </xsl:text>
								<xsl:value-of select="concat(substring($releaseDate, 9, 2), '/', substring($releaseDate, 6, 2), '/', substring($releaseDate,1,4))"/>
								<xsl:text>.</xsl:text>
							</xsl:when>
							<xsl:when test="@code='DVD'">
								<xsl:text>Sortie en DVD le : </xsl:text>
								<xsl:value-of select="concat(substring($releaseDate, 9, 2), '/', substring($releaseDate, 6, 2), '/', substring($releaseDate,1,4))"/>
								<xsl:text>.</xsl:text>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="concat(substring($releaseDate, 9, 2), '/', substring($releaseDate, 6, 2), '/', substring($releaseDate,1,4))"/>
								<xsl:text>.</xsl:text>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</s>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="medias">
		<xsl:if test="posters/media">
			<xsl:for-each select="posters/media">
				<f c="897">
					<s c="a"><xsl:value-of select="@src"/></s>
					<s c="b"><xsl:value-of select="."/></s>
				</f>
			</xsl:for-each>
		</xsl:if>
		<xsl:if test="trailers/media">
			<xsl:for-each select="trailers/media">
				<f c="897">
					<s c="a"><xsl:value-of select="@src"/></s>
					<s c="b"><xsl:value-of select="."/></s>
				</f>
			</xsl:for-each>
		</xsl:if>
		<xsl:if test="photos/media">
			<xsl:for-each select="photos/media">
				<f c="897">
					<s c="a"><xsl:value-of select="@src"/></s>
					<s c="b"><xsl:value-of select="."/></s>
				</f>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="cp_genre">
		<xsl:if test="genre">
			<xsl:for-each select="genre">
				<f c="900">
					<s c="a"><xsl:value-of select="."/></s>
					<s c="l">Genre of a film.</s>
					<s c="n">cp_genre</s>
				</f>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="cp_sub_genre">
		<xsl:if test="sub_genre">
			<xsl:for-each select="sub_genre">
				<f c="900">
					<s c="a"><xsl:value-of select="."/></s>
					<s c="n">cp_sub_genre</s>
				</f>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="cp_duration">
		<xsl:if test="duration">
			<xsl:for-each select="duration">
				<f c="900">
					<s c="a"><xsl:value-of select="."/></s>
					<s c="n">cp_duration</s>
				</f>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="cp_target_audience">
		<xsl:if test="target_audience">
			<xsl:for-each select="target_audience">
				<f c="900">
					<s c="a"><xsl:value-of select="."/></s>
					<s c="n">cp_target_audience</s>
				</f>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="cp_production_year">
		<xsl:if test="production_year">
			<xsl:for-each select="production_year">
				<f c="900">
					<s c="a"><xsl:value-of select="."/></s>
					<s c="n">cp_production_year</s>
				</f>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="cp_production_countries">
		<xsl:if test="production_countries/country">
			<xsl:for-each select="production_countries/country">
				<f c="900">
					<s c="a"><xsl:value-of select="@code"/></s>
					<s c="n">cp_production_countries</s>
				</f>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="cp_codes">
		<xsl:if test="codes/code">
			<xsl:for-each select="codes/code">
				<f c="900">
					<s c="a"><xsl:value-of select="."/></s>
					<s c="l">Define code type.</s>
					<s c="n">cp_codes</s>
				</f>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="cp_copyright">
		<xsl:if test="copyright">
			<xsl:for-each select="copyright">
				<f c="900">
					<s c="a"><xsl:value-of select="."/></s>
					<s c="l">Film Copyright.</s>
					<s c="n">cp_copyright</s>
				</f>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="cp_allowed_countries">
		<xsl:if test="allowed_countries/country">
			<xsl:for-each select="allowed_countries/country">
				<f c="900">
					<s c="a"><xsl:value-of select="@code"/></s>
					<s c="n">cp_allowed_countries</s>
				</f>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="cp_pk">
		<xsl:if test="pk">
			<xsl:for-each select="pk">
				<f c="900">
					<s c="a"><xsl:value-of select="."/></s>
					<s c="l">Film unique ID.</s>
					<s c="n">cp_pk</s>
				</f>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="source">
		<f c="801">
			<s c="a">FR</s>
			<s c="b">ArteVOD</s>
		</f>
	</xsl:template>
</xsl:stylesheet>