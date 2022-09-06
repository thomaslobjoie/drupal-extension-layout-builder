<?php

namespace Kbrodej\Drupal\DrupalExtension\Context;

use Behat\Gherkin\Node\TableNode;
use Drupal\block_content\BlockContentInterface;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
use Drupal\node\NodeInterface;
use Kbrodej\Drupal\Exceptions\EntityNotFoundException;
use Kbrodej\Drupal\Exceptions\LayoutBuilderNotSupportedException;
use Ramsey\Uuid\Uuid;

/**
 * Provides support for layout builder.
 */
class LayoutBuilderContext extends RawDrupalContext
{
    /**
     * Keep track of blocks, so they can be cleaned up.
     *
     * @var array
     */
    protected array $blocks = [];

    /**
     * @Given :type inline block(s):
     *
     * @param string $type
     * @param TableNode $blockTable
     */
    public function inlineBlocks(string $type, TableNode $blockTable): void
    {
        foreach ($blockTable->getHash() as $blockHash) {
            if (empty($blockHash['status'])) {
                $blockHash['status'] = 1;
            }

            $blockHash['uuid'] = Uuid::uuid4();
            $block = (object)$blockHash;
            $block->type = $type;
            $saved = $this->getDriver()->getCore()->entityCreate('block_content', $block);
            $this->blocks[] = $saved;
        }
    }

    /**
     * Remove any created blocks.
     *
     * @AfterScenario
     */
    public function cleanBlocks(): void
    {
        foreach ($this->blocks as $block) {
            $this->getDriver()->getCore()->entityDelete('block_content', $block);
        }
        $this->blocks = [];
    }

    /**
     * @Given I reset node :title layout
     *
     */
    public function iResetNodeLayout(string $title, string $sectionId): void
    {
        $node = $this->getDriver()->getCore()->loadContentByTitle('node', $title);
        if (is_null($node)) {
            throw new EntityNotFoundException(sprintf('Node with title %s not found', $title));
        }

        $sections[] = new Section($sectionId);
        $node->layout_builder__layout->setValue($sections);
        $node->save();
    }

    /**
     * @Given I attach block :block to :node in section :section
     *
     * @param string $block
     * @param string $node
     * @param string $sectionId
     *
     * @throws EntityStorageException
     */
    public function iAttachBlockToInSection(string $blockTitle, string $nodeTitle, string $sectionId): void
    {
        $loadedBlock = $this->getDriver()->getCore()
          ->loadContentByTitle('block_content', $blockTitle, 'info', 'changed');
        $loadedNode = $this->getDriver()->getCore()->loadContentByTitle('node', $nodeTitle);
        if (is_null($loadedBlock)) {
            throw new EntityNotFoundException(sprintf('BlockContentEntity with title %s not found!', $blockTitle));
        }


        if (is_null($loadedNode)) {
            throw new EntityNotFoundException(sprintf('Node with title %s not found!', $nodeTitle));
        }

        $this->attachBlockToParent($loadedNode, $loadedBlock, $sectionId);
    }

    /**
     * Attaches block to a parent node in a new given section.
     *
     * @param NodeInterface $node
     * @param BlockContentInterface $blockContent
     * @param string $sectionId
     *
     * @throws EntityStorageException
     */
    private function attachBlockToParent(
        NodeInterface $node,
        BlockContentInterface $blockContent,
        string $sectionId = 'layout_onecol'
    ): void {
        // @todo support adding instead of replacing new section and section components.
        if ($node->hasField('layout_builder__layout')) {
            $section = new Section($sectionId);

            $bundle = $blockContent->bundle();
            $pluginConfigurationId = sprintf('inline_block:%s', $bundle);
            $pluginConfiguration = [
                'id' => $pluginConfigurationId,
                'provider' => 'layout_builder',
                'label_display' => false,
                'view_mode' => 'default',
                'block_revision_id' => $blockContent->getRevisionId(),
            ];

            // Create a new section component using the node and plugin config.
            $component = new SectionComponent(
                Uuid::uuid4()->toString(),
                'content',
                $pluginConfiguration
            );

            // Add the component to the section.
            $section->appendComponent($component);

            // Add the section to the sections array.
            $sections[] = $section;

            // Set the sections.
            $node->set('layout_builder__layout', $sections);

            // Save the node.
            $node->save();
        } else {
            throw new LayoutBuilderNotSupportedException(
                sprintf('Layout builder not supported on bundle %s', $node->bundle())
            );
        }
    }
}
