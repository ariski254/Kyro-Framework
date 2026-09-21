<?php

namespace Core;

abstract class Migration {
    abstract public function up(): void;
    abstract public function down(): void;

    protected function execute(string $sql): bool {
        return Database::statement($sql);
    }

    protected function dropTableIfExists(string $table): bool {
        return $this->execute("DROP TABLE IF EXISTS `{$table}`");
    }
}
