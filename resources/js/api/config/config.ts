import type { QueryClient } from "@tanstack/react-query";

import type { Domain, SubDomains } from "./domains";

const ALL = "all";
const NO_DOMAIN = "";
type QueryKeySignature<TSubDomains extends string, TParams> = readonly [
  Domain | "",
  string | number,
  SubDomains<TSubDomains> | "",
  string,
  ...(TParams | undefined)[],
];

/**
 * Invalidates queries by domain
 *
 * @param queryClient the react-query client https://tanstack.com/query/v4/docs/react/reference/useQueryClient
 * @param queries: N arguments of either domain or [domain, id] tuples
 */
export function invalidateDomains(
  queryClient: QueryClient,
  ...queries: (Domain | [domain: Domain, id?: string | number | null])[]
) {
  queries.forEach((arg) => {
    const [domain, id] = typeof arg === "string" ? [arg] : arg;
    void queryClient.invalidateQueries([domain, ALL]);
    if (id !== undefined && id !== null && id !== ALL) {
      void queryClient.invalidateQueries([domain, id]);
    }
  });

  const subDomainsToInvalidate = queries.map((q) =>
    typeof q === "string" ? q : q[0],
  );
  console.log(subDomainsToInvalidate);

  console.log("invalidateDomains called with queries: ", queries);

  // Here we search all the cache for subdomains and clear those out because we have no way to check
  // if the data that was mutated will affect these endpoints or not
  queryClient
    .getQueryCache()
    .getAll()
    .forEach(({ queryKey }) => {
      const subDomains = queryKey.at(2);
      if (
        typeof subDomains === "string" &&
        subDomainsToInvalidate.some((d) => subDomains.split(",").includes(d))
      ) {
        console.log("Invalidating queryKey:", queryKey);
        void queryClient.invalidateQueries(queryKey);
      }
    });
}

/**
 * Invalidates queries by name
 *
 * @param queryClient the react-query client https://tanstack.com/query/v4/docs/react/reference/useQueryClient
 * @param queryName the unique identifier for the query, it's the main reason why you'd use this function
 * @param id the specific id we want to invalidate, if not given, we assume "all"
 */
export function invalidateQuery(
  queryClient: QueryClient,
  queryName: string,
  id?: string | number | null,
) {
  const paramsId = id ?? ALL;

  console.log(
    "invalidateQuery called with queryName: ",
    queryName,
    " and id: ",
    id,
  );

  queryClient
    .getQueryCache()
    .getAll()
    .forEach(({ queryKey }) => {
      if (queryName === queryKey.at(3) && paramsId === queryKey.at(1)) {
        console.log("Invalidating queryKey:", queryKey);
        void queryClient.invalidateQueries(queryKey);
      }
    });
}

/**
 * Normalizes the possibly empty, possibly already an array, params into an array
 * @param params the unknown value
 * @returns params converted to array
 */
function getParamsList<T>(params: T | T[]) {
  if (params !== null && params !== undefined) {
    return Array.isArray(params) ? params : [params];
  }
  return [];
}

/**
 * This function generates the query keys for you in the specific order we need
 *
 * @param queryName the literal name of the function calling the endpoint, serves as a unique identifier for this specific query since there could be multiple queries for the same domain that give different payloads
 * @param options.domain the main domain being queried
 * @param options.id if specified, the id of the value inside the domain that is mutated. Primarily used to later invalidate queries for THIS id in THIS domain
 * @param options.subDomains comma separated domains whose data is also included in the query
 * @param options.params extra data, usually query params like filtering, sorting, pagination, etc
 *
 */
export function generateQueryKey<TSubDomains extends string, TParams>(
  queryName: string,
  {
    domain,
    id,
    subDomains,
    params,
  }: {
    domain?: Domain;
    id?: string | number | null;
    subDomains?: SubDomains<TSubDomains>;
    params?: TParams | TParams[];
  } = {},
) {
  const queryKey: QueryKeySignature<TSubDomains, TParams> = [
    domain ?? NO_DOMAIN,
    id ?? ALL,
    subDomains ?? NO_DOMAIN,
    queryName,
    ...getParamsList(params),
  ] as const;

  return queryKey;
}
