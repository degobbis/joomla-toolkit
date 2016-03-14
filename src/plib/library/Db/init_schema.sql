CREATE TABLE IF NOT EXISTS `installations` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `subscriptionId` INTEGER NOT NULL,
  `sitename` TEXT NOT NULL,
  `path` TEXT NOT NULL,
  `version` TEXT NOT NULL,
  `newVersion` TEXT,
  `needsUpdate` INTEGER NOT NULL DEFAULT 0
);
CREATE TABLE IF NOT EXISTS `extensions` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `installationId` INTEGER NOT NULL REFERENCES installations(id) ON DELETE CASCADE,
  `joomlaId` INT,
  `name` TEXT NOT NULL,
  `currentVersion` TEXT,
  `newVersion` TEXT,
  `needsUpdate` INTEGER NOT NULL
);
