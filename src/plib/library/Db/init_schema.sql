CREATE TABLE IF NOT EXISTS `installations` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `subscriptionId` INTEGER NOT NULL,
  `sitename` TEXT NOT NULL,
  `path` TEXT NOT NULL,
  `version` TEXT NOT NULL
);
