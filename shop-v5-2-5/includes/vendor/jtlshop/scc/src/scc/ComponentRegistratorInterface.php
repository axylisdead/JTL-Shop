<?php declare(strict_types=1);

namespace scc;

/**
 * Interface ComponentRegistratorInterface
 * @package scc
 */
interface ComponentRegistratorInterface
{
    /**
     * ComponentRegistratorInterface constructor.
     * @param RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer);

    /**
     *
     */
    public function registerComponents(): void;

    /**
     * @return RendererInterface
     */
    public function getRenderer(): RendererInterface;

    /**
     * @param RendererInterface $renderer
     */
    public function setRenderer(RendererInterface $renderer): void;

    /**
     * @return ComponentInterface[]
     */
    public function getComponents(): array;

    /**
     * @param ComponentInterface[] $components
     */
    public function setComponents(array $components): void;
}
