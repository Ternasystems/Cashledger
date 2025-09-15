<?php

namespace API_ProfilingRepositories_Contract;

/**
 * An empty "marker" interface to identify entities that are part of the
 * user profiling system.
 *
 * This allows for high-level, polymorphic checks, for example:
 * if ($someObject instanceof IProfile) { ... }
 */
interface IProfile
{
}