<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260323114030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE campaign_role (role VARCHAR(24) NOT NULL, user_id INT NOT NULL, campaign_id INT NOT NULL, PRIMARY KEY (user_id, campaign_id))');
        $this->addSql('CREATE INDEX IDX_568EB1889D86650F ON campaign_role (user_id)');
        $this->addSql('CREATE INDEX IDX_568EB1883141FA38 ON campaign_role (campaign_id)');
        $this->addSql('ALTER TABLE campaign_role ADD CONSTRAINT FK_568EB1889D86650F FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE campaign_role ADD CONSTRAINT FK_568EB1883141FA38 FOREIGN KEY (campaign_id) REFERENCES campaign (id) NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campaign_role DROP CONSTRAINT FK_568EB1889D86650F');
        $this->addSql('ALTER TABLE campaign_role DROP CONSTRAINT FK_568EB1883141FA38');
        $this->addSql('DROP TABLE campaign_role');
    }
}
