#
#
# Struttura della tabella `php_stats_cache`
#

CREATE TABLE IF NOT EXISTS php_stats_cache (
  user_id int(10) unsigned NOT NULL default '0',
  data int(11) unsigned NOT NULL default '0',
  lastpage varchar(255) NOT NULL default '0',
  visitor_id varchar(32) NOT NULL default '',
  hits tinyint(3) unsigned NOT NULL default '0',
  visits smallint(5) unsigned NOT NULL default '0',
  reso varchar(10) NOT NULL default '',
  colo varchar(10) NOT NULL default '',
  os varchar(20) NOT NULL default '',
  bw varchar(20) NOT NULL default '',
  host varchar(80) NOT NULL default '',
  tld varchar(7) NOT NULL default 'unknown',
  lang varchar(8) NOT NULL default '',
  giorno varchar(10) NOT NULL default '',
  notbrowser tinyint(1) NOT NULL default '0',
  level tinyint(3) unsigned NOT NULL default '0',
  UNIQUE KEY user_id (user_id)
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_cache`
#


# --------------------------------------------------------


#
# Struttura della tabella `php_stats_clicks`
#

CREATE TABLE IF NOT EXISTS php_stats_clicks (
  id int(11) NOT NULL auto_increment,
  nome varchar(20) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  creazione int(11) unsigned NOT NULL default '0',
  clicks int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

#
# Dump dei dati per la tabella `php_stats_clicks`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_config`
#

CREATE TABLE IF NOT EXISTS php_stats_config (
  name varchar(20) NOT NULL default '',
  value varchar(255) NOT NULL default '',
  PRIMARY KEY  (name)
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_config`
#

INSERT IGNORE INTO php_stats_config VALUES ('stats_disabled', '0');
INSERT IGNORE INTO php_stats_config VALUES ('language', 'it');
INSERT IGNORE INTO php_stats_config VALUES ('server_url', 'http://www.tuosito.it');
INSERT IGNORE INTO php_stats_config VALUES ('admin_pass', '123456');
INSERT IGNORE INTO php_stats_config VALUES ('use_pass', '0');
INSERT IGNORE INTO php_stats_config VALUES ('unlock_pages','0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|');
INSERT IGNORE INTO php_stats_config VALUES ('cifre', '8');
INSERT IGNORE INTO php_stats_config VALUES ('stile', '1');
INSERT IGNORE INTO php_stats_config VALUES ('timezone', '0');
INSERT IGNORE INTO php_stats_config VALUES ('template', 'default');
INSERT IGNORE INTO php_stats_config VALUES ('startvisits', '0');
INSERT IGNORE INTO php_stats_config VALUES ('starthits', '0');
INSERT IGNORE INTO php_stats_config VALUES ('nomesito', 'Tuo Sito o link');
INSERT IGNORE INTO php_stats_config VALUES ('moduli', '1|2|1|2|2|2|1|1|1|1|1|1|');
INSERT IGNORE INTO php_stats_config VALUES ('user_mail', 'tuonome@tuoserver.it');
INSERT IGNORE INTO php_stats_config VALUES ('user_pass_new', '');
INSERT IGNORE INTO php_stats_config VALUES ('user_pass_key', '');
INSERT IGNORE INTO php_stats_config VALUES ('prune_0_on', '0');
INSERT IGNORE INTO php_stats_config VALUES ('prune_0_value', '48');
INSERT IGNORE INTO php_stats_config VALUES ('prune_1_on', '0');
INSERT IGNORE INTO php_stats_config VALUES ('prune_1_value', '500');
INSERT IGNORE INTO php_stats_config VALUES ('prune_2_on', '0');
INSERT IGNORE INTO php_stats_config VALUES ('prune_2_value', '200');
INSERT IGNORE INTO php_stats_config VALUES ('prune_3_on', '0');
INSERT IGNORE INTO php_stats_config VALUES ('prune_3_value', '200');
INSERT IGNORE INTO php_stats_config VALUES ('prune_4_on', '0');
INSERT IGNORE INTO php_stats_config VALUES ('prune_4_value', '200');
INSERT IGNORE INTO php_stats_config VALUES ('prune_5_on', '0');
INSERT IGNORE INTO php_stats_config VALUES ('prune_5_value', '200');
REPLACE INTO php_stats_config VALUES ('phpstats_ver', '1.4');
INSERT IGNORE INTO php_stats_config VALUES ('inadm_last_update', '1256716440');
INSERT IGNORE INTO php_stats_config VALUES ('inadm_lastcache_time', '0');
REPLACE INTO php_stats_config VALUES ('inadm_upd_available', '0');
INSERT IGNORE INTO php_stats_config VALUES ('ip_timeout', '4');
INSERT IGNORE INTO php_stats_config VALUES ('page_timeout', '1200');
INSERT IGNORE INTO php_stats_config VALUES ('report_w_on', '0');
INSERT IGNORE INTO php_stats_config VALUES ('report_w_day', '0');
INSERT IGNORE INTO php_stats_config VALUES ('instat_report_w', '0');
INSERT IGNORE INTO php_stats_config VALUES ('instat_max_online', '0|0');
INSERT IGNORE INTO php_stats_config VALUES ('auto_optimize', '0');
INSERT IGNORE INTO php_stats_config VALUES ('auto_opt_every', '500');
INSERT IGNORE INTO php_stats_config VALUES ('exc_fol','');
INSERT IGNORE INTO php_stats_config VALUES ('exc_sip','');
INSERT IGNORE INTO php_stats_config VALUES ('exc_dip','');

# --------------------------------------------------------

#
# Struttura della tabella `php_stats_counters`
#

CREATE TABLE IF NOT EXISTS php_stats_counters (
  hits int(11) unsigned NOT NULL default '0',
  visits int(11) unsigned NOT NULL default '0',
  no_count_hits int(11) unsigned NOT NULL default '0',
  no_count_visits int(11) unsigned NOT NULL default '0'
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_counters`
#

INSERT IGNORE INTO php_stats_counters VALUES (0, 0, 0, 0);

# --------------------------------------------------------

#
# Struttura della tabella `php_stats_daily`
#

CREATE TABLE IF NOT EXISTS php_stats_daily (
  data date NOT NULL default '0000-00-00',
  hits int(11) NOT NULL default '0',
  visits int(11) NOT NULL default '0',
  no_count_hits int(11) NOT NULL default '0',
  no_count_visits int(11) NOT NULL default '0',
  rets int(11) NOT NULL default '0',
  PRIMARY KEY  (data)
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_daily`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_details`
#

CREATE TABLE IF NOT EXISTS php_stats_details (
  visitor_id varchar(50) NOT NULL default '',
  ip int(10) unsigned NOT NULL default '0',
  host varchar(80) NOT NULL default '',
  os varchar(20) NOT NULL default '',
  bw varchar(20) NOT NULL default '',
  lang varchar(10) NOT NULL default '',
  date int(11) unsigned NOT NULL default '0',
  referer longtext NOT NULL default '',
  currentPage varchar(255) NOT NULL default '',
  reso varchar(10) NOT NULL default '',
  colo varchar(10) NOT NULL default '',
  titlePage varchar(255) NOT NULL default '',
  tld varchar(7) NOT NULL DEFAULT 'unknown',
  rets int(11) NOT NULL default '0',
  last_return int(11) unsigned NOT NULL default '0'
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_details`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_domains`
#

CREATE TABLE IF NOT EXISTS php_stats_domains (
  visits int(11) NOT NULL default '0',
  hits int(11) NOT NULL default '0',
  tld varchar(8) NOT NULL default '',
  area varchar(4) NOT NULL default '',
  PRIMARY KEY  (tld)
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_domains`
#

INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ac','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ad','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ae','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'af','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ag','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ai','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'al','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'am','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'an','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ao','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'aq','AN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ar','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'as','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'at','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'au','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'aw','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'az','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ba','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'bb','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'bd','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'be','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'bf','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'bg','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'bh','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'bi','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'bj','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'bm','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'bn','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'bo','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'br','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'bs','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'bt','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'bv','AN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'bw','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'by','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'bz','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ca','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'cc','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'cd','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'cf','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'cg','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ch','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ci','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ck','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'cl','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'cm','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'cn','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'co','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'cr','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'cu','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'cv','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'cx','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'cy','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'cz','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'de','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'dj','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'dk','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'dm','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'do','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'dz','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ec','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ee','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'eg','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'eh','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'er','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'es','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'et','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'fi','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'fj','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'fk','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'fm','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'fo','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'fr','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ga','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'gd','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ge','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'gf','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'gg','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'gh','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'gi','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'gl','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'gm','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'gn','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'gp','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'gq','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'gr','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'gs','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'gt','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'gu','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'gw','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'gy','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'hk','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'hm','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'hn','AN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'hr','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ht','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'hu','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'id','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ie','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'il','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'im','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'in','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'io','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'iq','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ir','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'is','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'it','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'je','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'jm','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'jo','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'jp','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ke','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'kg','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'kh','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ki','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'km','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'kn','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'kp','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'kr','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'kw','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ky','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'kz','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'la','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'lb','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'lc','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'li','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'lk','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'lr','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ls','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'lt','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'lu','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'lv','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ly','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ma','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'mc','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'md','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'mg','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'mh','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'mk','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ml','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'mm','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'mn','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'mo','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'mp','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'mq','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'mr','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ms','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'mt','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'mu','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'mv','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'mw','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'mx','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'my','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'mz','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'na','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'nc','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ne','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'nf','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ng','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ni','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'nl','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'no','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'np','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'nr','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'nu','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'nz','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'om','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'pa','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'pe','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'pf','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'pg','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ph','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'pk','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'pl','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'pm','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'pn','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'pr','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ps','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'pt','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'pw','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'py','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'qa','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'re','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ro','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ru','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'rw','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'sa','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'sb','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'sc','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'sd','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'se','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'sg','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'sh','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'si','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'sj','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'sk','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'sl','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'sm','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'sn','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'so','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'sr','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'st','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'sv','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'sy','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'sz','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'tc','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'td','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'tf','AN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'tg','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'th','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'tj','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'tk','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'tm','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'tn','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'to','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'tp','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'tr','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'tt','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'tv','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'tw','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'tz','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ua','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ug','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'uk','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'gb','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'um','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'us','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'uy','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'uz','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'va','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'vc','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ve','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'vg','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'vi','AM');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'vn','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'vu','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'wf','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ws','OZ');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'ye','AS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'yt','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'yu','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'za','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'zm','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'zr','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'zw','AF');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'eu','EU');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'com','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'net','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'org','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'edu','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'int','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'arpa','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'gov','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'mil','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'su','GUS');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'arts','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'firm','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'info','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'nom','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'rec','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'shop','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'web','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'biz','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'pro','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'coop','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'museum','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'aero','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'name','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'nato','UN');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'lan','');
INSERT IGNORE INTO php_stats_domains VALUES (0,0,'unknown','');

# --------------------------------------------------------

#
# Struttura della tabella `php_stats_downloads`
#

CREATE TABLE IF NOT EXISTS php_stats_downloads (
  id int(11) NOT NULL auto_increment,
  nome varchar(255) NOT NULL default '',
  descrizione varchar(255) NOT NULL default '',
  type varchar(20) NOT NULL default '',
  home varchar(255) NOT NULL default '',
  size varchar(20) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  creazione int(11) unsigned NOT NULL default '0',
  downloads int(11) NOT NULL default '0',
  withinterface enum('YES','NO') NOT NULL default 'NO',
  PRIMARY KEY  (id)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

#
# Dump dei dati per la tabella `php_stats_downloads`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_hourly`
#

CREATE TABLE IF NOT EXISTS php_stats_hourly (
  data tinyint(4) NOT NULL default '0',
  hits int(11) unsigned NOT NULL default '0',
  visits int(11) unsigned NOT NULL default '0',
  no_count_hits int(11) unsigned NOT NULL default '0',
  no_count_visits int(11) unsigned NOT NULL default '0',
  mese varchar(8) NOT NULL default ''
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_hourly`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_ip`
#

CREATE TABLE IF NOT EXISTS php_stats_ip (
  ip int(10) unsigned NOT NULL default '0',
  date int(11) unsigned NOT NULL default '0',
  hits int(11) unsigned NOT NULL default '0',
  visits int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (ip)
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_ip`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_langs`
#

CREATE TABLE IF NOT EXISTS php_stats_langs (
  lang varchar(8) NOT NULL default '',
  hits int(11) unsigned NOT NULL default '0',
  visits int(11) unsigned NOT NULL default '0',
  UNIQUE KEY lang (lang) 
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_langs`
#

INSERT IGNORE INTO php_stats_langs VALUES ('unknown', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('af', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('sq', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ar-dz', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ar-bh', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ar-eg', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ar-iq', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ar-jo', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ar-kw', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ar-lb', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ar-ly', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ar-ma', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ar-om', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ar-qa', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ar-sa', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ar-sy', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ar-tn', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ar-ae', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ar-ye', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ar', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('hy', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('as', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('az', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('eu', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('be', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('bn', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('bg', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ca', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('zh-cn', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('zh-hk', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('zh-mo', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('zh-sg', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('zh-tw', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('zh', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('hr', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('cs', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('da', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('div', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('nl-be', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('nl', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('en-au', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('en-bz', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('en-ca', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('en', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('en-ie', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('en-jm', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('en-nz', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('en-ph', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('en-za', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('en-tt', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('en-gb', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('en-us', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('en-zw', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('et', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('fo', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('fa', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('fi', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('fr-be', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('fr-ca', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('fr', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('fr-lu', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('fr-mc', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('fr-ch', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('mk', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('gd', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ka', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('de-at', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('de', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('de-li', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('de-lu', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('de-ch', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('el', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('gu', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('he', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('hi', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('hu', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('is', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('id', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('it', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('it-ch', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ja', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('kn', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('kk', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('kok', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ko', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('kz', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('lv', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('lt', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ms', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ml', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('mt', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('mr', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('mn', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ne', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('nb-no', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('no', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('nn-no', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('or', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('pl', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('pt-br', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('pt', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('pa', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('rm', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ro-md', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ro', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ru-md', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ru', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('sa', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('sr', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('sk', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ls', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('sb', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-ar', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-bo', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-cl', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-co', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-cr', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-do', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-ec', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-sv', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-gt', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-hn', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-mx', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-ni', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-pa', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-py', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-pe', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-pr', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-us', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-uy', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('es-ve', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('sx', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('sw', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('sv-fi', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('sv', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('syr', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ta', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('tt', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('te', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('th', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ts', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('tn', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('tr', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('uk', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('ur', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('uz', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('vi', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('xh', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('yi', 0, 0);
INSERT IGNORE INTO php_stats_langs VALUES ('zu', 0, 0);

# --------------------------------------------------------

#
# Struttura della tabella `php_stats_pages`
#

CREATE TABLE IF NOT EXISTS php_stats_pages (
  data varchar(255) NOT NULL default '0',
  hits int(11) unsigned NOT NULL default '0',
  visits int(11) unsigned NOT NULL default '0',
  no_count_hits int(11) unsigned NOT NULL default '0',
  no_count_visits int(11) unsigned NOT NULL default '0',
  presence bigint(20) unsigned default '0',
  tocount int(10) unsigned NOT NULL default '0',
  date int(11) unsigned NOT NULL default '0',
  lev_1 int(10) NOT NULL default '0',
  lev_2 int(10) NOT NULL default '0',
  lev_3 int(10) NOT NULL default '0',
  lev_4 int(10) NOT NULL default '0',
  lev_5 int(10) NOT NULL default '0',
  lev_6 int(10) NOT NULL default '0',
  outs int(10) NOT NULL default '0',
  titlePage varchar(255) NOT NULL default '',
  UNIQUE KEY  (data)
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_pages`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_query`
#

CREATE TABLE IF NOT EXISTS php_stats_query (
  data varchar(255) binary NOT NULL default '',
  engine varchar(30) NOT NULL default '',
  domain varchar(8) NOT NULL default 'unknown',
  page smallint(6) NOT NULL default '0',
  visits int(11) NOT NULL default '0',
  date int(11) unsigned NOT NULL default '0',
  mese varchar(8) NOT NULL default ''
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_query`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_referer`
#

CREATE TABLE IF NOT EXISTS php_stats_referer (
  data varchar(255) NOT NULL default '0',
  visits int(11) NOT NULL default '0',
  date int(11) unsigned NOT NULL default '0',
  mese varchar(8) NOT NULL default ''
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_referer`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_systems`
#

CREATE TABLE IF NOT EXISTS php_stats_systems (
  os varchar(20) NOT NULL default '',
  bw varchar(20) NOT NULL default '',
  reso varchar(10) NOT NULL default '',
  colo varchar(10) NOT NULL default '',
  hits int(11) NOT NULL default '0',
  visits int(11) NOT NULL default '0',
  mese varchar(8) NOT NULL default '',
  UNIQUE KEY (os,bw,reso,colo,mese)
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_systems`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_links`
#

CREATE TABLE IF NOT EXISTS php_stats_links (
data date NOT NULL default '0000-00-00',
url varchar(255) NOT NULL default ''
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_links`
#
