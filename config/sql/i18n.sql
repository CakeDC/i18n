#==========================================================================#
#                                                                          #
# Copyright 2007-2008 Cake Development Corporation                         #
#                     1785 E. Sahara Avenue, Suite 490-423                 #
#                     Las Vegas, Nevada 89104                              #
#                                                                          #
# Use of this file permitted only with written permission from the         #
# copyright holder listed above                                            #
#==========================================================================#
DROP TABLE IF EXISTS i18n;

CREATE TABLE i18n (
  id CHAR(36) NOT NULL,
  locale VARCHAR(3) NOT NULL,
  model VARCHAR(255) NOT NULL,
  foreign_key CHAR(36) NOT NULL,
  field VARCHAR(64) NOT NULL,
  content MEDIUMTEXT NULL,
  PRIMARY KEY(id),
  UNIQUE INDEX I18N_LOCALE_FIELD(locale, model(230), foreign_key, field),
  INDEX I18N_LOCALE_ROW(locale, model(230), foreign_key),
  INDEX I18N_LOCALE_MODEL(locale, model(230)),
  INDEX I18N_FIELD(model(230), foreign_key, field),
  INDEX I18N_ROW(model(230), foreign_key)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;